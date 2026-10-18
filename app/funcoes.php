<?php
// Funções auxiliares usadas pelas páginas e componentes.

declare(strict_types=1);

define('APP', __DIR__);
define('RAIZ', dirname(__DIR__));          // raiz do site (= raiz do repositório = public_html na Hostinger)
define('CONTEUDO', RAIZ . '/conteudo');    // dados gravados pelo painel no servidor (fora do Git)
defined('PUBLICO') || define('PUBLICO', RAIZ);

require_once APP . '/config.php';
require_once APP . '/blog.php';

garantir_conteudo();
renovar_cache_apos_publicacao();

/**
 * Na primeira execução no servidor, cria conteudo/ e copia os artigos de dados-iniciais/.
 * Depois disso, o blog vive em conteudo/ (gravado pelo painel) e o Git nunca mexe nele.
 */
function garantir_conteudo(): void
{
    if (is_dir(CONTEUDO . '/blog')) return;
    @mkdir(CONTEUDO . '/blog', 0755, true);
    foreach (glob(RAIZ . '/dados-iniciais/blog/*.json') ?: [] as $arquivo) {
        @copy($arquivo, CONTEUDO . '/blog/' . basename($arquivo));
    }
    // Proteção extra caso o .htaccess principal seja alterado
    @file_put_contents(CONTEUDO . '/.htaccess', "Require all denied\n");
}

/**
 * O LiteSpeed da Hostinger guarda as páginas prontas por 1 hora (.htaccess). Depois de uma publicação pelo GitHub,
 * a primeira página gerada pelo PHP percebe que o código mudou e pede para apagar esse cache; assim ninguém vê a
 * versão antiga (por exemplo, botões com o link de antes). A "impressão digital" é a data e o tamanho dos arquivos
 * do site, que mudam a cada publicação; ela fica guardada em conteudo/ (fora do Git).
 */
function renovar_cache_apos_publicacao(): void
{
    $arquivos = array_merge(
        [RAIZ . '/index.php', RAIZ . '/.htaccess'],
        glob(APP . '/*.php') ?: [],
        glob(APP . '/{componentes,layout,paginas,dados}/*', GLOB_BRACE) ?: [],
        glob(RAIZ . '/assets/{css,js}/*', GLOB_BRACE) ?: []
    );
    $digital = '';
    foreach ($arquivos as $arquivo) {
        $digital .= $arquivo . ':' . @filemtime($arquivo) . ':' . @filesize($arquivo) . "
";
    }
    $digital = md5($digital);

    $registro = CONTEUDO . '/.versao-do-site';
    if (@file_get_contents($registro) === $digital) return;
    // Só pede a limpeza se conseguir registrar a versão (evita limpar o cache a cada visita se a pasta não aceitar gravação)
    if (@file_put_contents($registro, $digital, LOCK_EX) !== false && !headers_sent()) {
        header('X-LiteSpeed-Purge: *');
    }
}

// ---------------------------------------------------------------- Texto

/** Escapa texto para HTML. Use sempre que imprimir dados: <?= e($texto) ?> */
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Minúsculas e sem acentos, para comparar textos. */
function normalizar(string $texto): string
{
    $sem = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
    return strtolower($sem !== false ? $sem : $texto);
}

/** "2025-07-23" -> "23 de julho de 2025" */
function data_extenso(string $ymd): string
{
    $meses = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
    [$a, $m, $d] = array_map('intval', explode('-', substr($ymd, 0, 10)));
    return sprintf('%02d de %s de %d', $d, $meses[$m - 1], $a);
}

// ---------------------------------------------------------------- SEO

/** Acrescenta " | GastriClin" só quando o título continua com até 60 caracteres. */
function titulo_com_marca(string $titulo, int $limite = 60): string
{
    $com = $titulo . ' | GastriClin';
    return mb_strlen($com) <= $limite ? $com : $titulo;
}

/** Corta no último espaço antes do limite e acrescenta reticências. */
function limitar(string $texto, int $limite = 155): string
{
    if (mb_strlen($texto) <= $limite) return $texto;
    $corte = mb_substr($texto, 0, $limite - 1);
    $corte = mb_substr($corte, 0, (int) mb_strrpos($corte, ' '));
    return rtrim($corte, ",;:.\u{2013}- ") . '…';
}

function json_ld(array $dados): string
{
    $json = json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return '<script type="application/ld+json">' . str_replace('</', '<\/', $json) . '</script>';
}

/** Caminho da página atual, sempre com barra no fim (ex.: "/endoscopia/"). */
function caminho_atual(): string
{
    $p = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return rtrim($p, '/') . '/';
}

// ---------------------------------------------------------------- Dados

function servicos(): array
{
    static $s;
    return $s ??= require APP . '/dados/servicos.php';
}

function consultas(): array
{
    return array_values(array_filter(servicos(), fn($s) => $s['tipo'] === 'consulta'));
}

function exames(): array
{
    return array_values(array_filter(servicos(), fn($s) => $s['tipo'] === 'exame'));
}

function servico(string $slug): ?array
{
    foreach (servicos() as $s) if ($s['slug'] === $slug) return $s;
    return null;
}

function profissionais(): array
{
    static $p;
    return $p ??= require APP . '/dados/medicos.php';
}

function profissionais_ativos(): array
{
    return array_values(array_filter(profissionais(), fn($p) => $p['ativo']));
}

function medicos_ativos(): array
{
    return array_values(array_filter(profissionais_ativos(), fn($p) => $p['medico']));
}

function profissional(string $slug): ?array
{
    foreach (profissionais_ativos() as $p) if ($p['slug'] === $slug) return $p;
    return null;
}

function medicos_do_servico(string $slug): array
{
    return array_values(array_filter(medicos_ativos(), fn($m) => in_array($slug, $m['servicos'], true)));
}

function nome_exibicao(array $p): string
{
    return $p['tratamento'] ? $p['tratamento'] . ' ' . $p['nome'] : $p['nome'];
}

function convenios(): array
{
    static $c;
    return $c ??= require APP . '/dados/convenios.php';
}

// ---------------------------------------------------------------- Contato e agendamento

function mensagem_agendamento(?string $servico = null): string
{
    return $servico
        ? "Olá! Vim pelo site da GastriClin e gostaria de agendar: {$servico}."
        : SITE['whatsapp']['mensagem_padrao'];
}

function whatsapp_url(?string $mensagem = null): string
{
    return 'https://wa.me/' . SITE['whatsapp']['numero'] . '?text=' . rawurlencode($mensagem ?? SITE['whatsapp']['mensagem_padrao']);
}

// ---------------------------------------------------------------- Componentes

/** Renderiza app/componentes/<nome>.php com as variáveis informadas. */
function componente(string $nome, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    require APP . "/componentes/{$nome}.php";
}

/** Mesmo que componente(), mas devolve o HTML como texto (para passar a outro componente). */
function componente_html(string $nome, array $vars = []): string
{
    ob_start();
    componente($nome, $vars);
    return (string) ob_get_clean();
}

/** Ícone do sprite (app/componentes/icones.svg). */
function icone(string $nome, int $tamanho = 24, string $classe = '', ?string $rotulo = null): string
{
    // Ícones de anatomia (Health Icons) usam grade 48x48 e são preenchidos; os de interface são de traço, 24x24
    $saude = ['estetoscopio', 'estomago', 'endoscopio', 'colon', 'intestino', 'polipo'];
    $preenchido = $nome === 'whatsapp' || in_array($nome, $saude, true);
    $attrs = sprintf(
        'viewBox="0 0 %1$d %1$d" width="%2$d" height="%2$d" fill="%3$s" stroke="%4$s" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"',
        in_array($nome, $saude, true) ? 48 : 24,
        $tamanho,
        $preenchido ? 'currentColor' : 'none',
        $preenchido ? 'none' : 'currentColor'
    );
    $classe = $classe ? ' class="' . e($classe) . '"' : '';
    $aria = $rotulo ? ' role="img" aria-label="' . e($rotulo) . '"' : ' aria-hidden="true"';
    return "<svg {$attrs}{$classe}{$aria}><use href=\"#i-{$nome}\"></use></svg>";
}

// ---------------------------------------------------------------- Imagens

function manifesto_imagens(): array
{
    static $m;
    return $m ??= json_decode((string) file_get_contents(APP . '/dados/imagens.json'), true);
}

/**
 * <img> responsivo em WebP a partir de app/dados/imagens.json.
 * Opções: sizes, larguras (lista), largura ou altura (tamanho fixo exibido, gera 1x/2x),
 *         classe, carregar ('lazy' | 'eager'), prioridade (true = fetchpriority high).
 */
function imagem(string $chave, string $alt, array $o = []): string
{
    $info = manifesto_imagens()[$chave] ?? null;
    if (!$info) return '<!-- imagem não encontrada: ' . e($chave) . ' -->';
    return montar_img('/assets/img/' . $chave, $info['w'], $info['h'], $info['larguras'], $alt, $o + ['versao' => $info['v'] ?? '']);
}

/** <img> da capa de um artigo (dados gravados no JSON do artigo). */
function imagem_capa(array $capa, string $alt, array $o = []): string
{
    return montar_img('/' . $capa['arquivo'], $capa['w'], $capa['h'], $capa['larguras'], $alt, $o);
}

function montar_img(string $base, int $w, int $h, array $larguras, string $alt, array $o): string
{
    sort($larguras);
    // ?v= muda quando a foto é trocada (ferramentas/gerar_imagens.py): o navegador baixa a nova em vez de usar a do cache
    $v = empty($o['versao']) ? '' : '?v=' . $o['versao'];
    $url = fn(int $lw) => "{$base}-{$lw}.webp{$v}";
    $exibida = $o['largura'] ?? (isset($o['altura']) ? (int) round($o['altura'] * $w / $h) : null);

    if ($exibida) {
        // Tamanho fixo: escolhe as larguras mais próximas de 1x e 2x
        $escolher = function (int $alvo) use ($larguras): int {
            foreach ($larguras as $lw) if ($lw >= $alvo) return $lw;
            return end($larguras);
        };
        $l1 = $escolher($exibida);
        $l2 = $escolher($exibida * 2);
        $src = $url($l1);
        $srcset = $l1 === $l2 ? '' : $url($l1) . ' 1x, ' . $url($l2) . ' 2x';
        $largura = $exibida;
        $altura = (int) round($exibida * $h / $w);
        $sizes = '';
    } else {
        $usar = $o['larguras'] ?? $larguras;
        $usar = array_values(array_intersect($larguras, $usar)) ?: $larguras;
        $src = $url($usar[(int) floor((count($usar) - 1) / 2)]);
        $srcset = implode(', ', array_map(fn($lw) => $url($lw) . " {$lw}w", $usar));
        $largura = $w;
        $altura = $h;
        $sizes = $o['sizes'] ?? '100vw';
    }

    $attrs = ['src="' . e($src) . '"'];
    if ($srcset) $attrs[] = 'srcset="' . e($srcset) . '"';
    if ($sizes) $attrs[] = 'sizes="' . e($sizes) . '"';
    $attrs[] = 'alt="' . e($alt) . '"';
    $attrs[] = "width=\"{$largura}\" height=\"{$altura}\"";
    if (!empty($o['classe'])) $attrs[] = 'class="' . e($o['classe']) . '"';
    $attrs[] = 'loading="' . (($o['carregar'] ?? 'lazy') === 'eager' ? 'eager' : 'lazy') . '"';
    if (!empty($o['prioridade'])) $attrs[] = 'fetchpriority="high"';
    $attrs[] = 'decoding="async"';
    return '<img ' . implode(' ', $attrs) . '>';
}

// ---------------------------------------------------------------- CSS

/** CSS embutido no HTML (sem requisição bloqueante), minificado na hora. */
function css_embutido(): string
{
    $css = '';
    foreach (['fontes.css', 'site.css'] as $arquivo) {
        $css .= file_get_contents(PUBLICO . '/assets/css/' . $arquivo);
    }
    $css = preg_replace('!/\*.*?\*/!s', '', $css);
    $css = preg_replace('/\s+/', ' ', $css);
    $css = preg_replace('/\s*([{};,>])\s*/', '$1', $css);
    $css = str_replace(';}', '}', $css);
    return trim($css);
}
