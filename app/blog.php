<?php
// Artigos do blog: um arquivo JSON por artigo em conteudo/blog/<slug>.json.
// Campos: slug, titulo, titulo_seo, descricao, status (rascunho | publicado),
//         publicado_em, atualizado_em, categorias, palavras_chave, capa, revisao_medica, conteudo_html.

declare(strict_types=1);

const STATUS_POST = [
    'rascunho' => 'Rascunho',
    'publicado' => 'Publicado',
];

function pasta_blog(): string
{
    return CONTEUDO . '/blog';
}

/** Todos os artigos, do mais recente para o mais antigo. */
function posts(bool $somente_publicados = true): array
{
    static $cache = [];
    $chave = $somente_publicados ? 'pub' : 'todos';
    if (isset($cache[$chave])) return $cache[$chave];

    $lista = [];
    foreach (glob(pasta_blog() . '/*.json') ?: [] as $arquivo) {
        $post = json_decode((string) file_get_contents($arquivo), true);
        if (!is_array($post)) continue;
        if ($somente_publicados && ($post['status'] ?? '') !== 'publicado') continue;
        $lista[] = $post;
    }
    usort($lista, fn($a, $b) => strcmp($b['publicado_em'], $a['publicado_em']) ?: strcmp($a['slug'], $b['slug']));
    return $cache[$chave] = $lista;
}

function post(string $slug, bool $somente_publicados = true): ?array
{
    if (!slug_valido($slug)) return null;
    $arquivo = pasta_blog() . "/{$slug}.json";
    if (!is_file($arquivo)) return null;
    $post = json_decode((string) file_get_contents($arquivo), true);
    if (!is_array($post)) return null;
    if ($somente_publicados && ($post['status'] ?? '') !== 'publicado') return null;
    return $post;
}

/** Artigos com mais categorias em comum. */
function posts_relacionados(array $post, int $quantidade = 3): array
{
    $cats = $post['categorias'] ?? [];
    $outros = array_filter(posts(), fn($p) => $p['slug'] !== $post['slug']);
    usort($outros, function ($a, $b) use ($cats) {
        $na = count(array_intersect($a['categorias'] ?? [], $cats));
        $nb = count(array_intersect($b['categorias'] ?? [], $cats));
        return $nb <=> $na ?: strcmp($b['publicado_em'], $a['publicado_em']);
    });
    return array_slice($outros, 0, $quantidade);
}

/** Artigos cujo título ou palavras-chave citam os temas de um serviço. */
function posts_por_temas(array $temas, int $quantidade = 3): array
{
    $temas = array_map('normalizar', $temas);
    $pontuados = [];
    foreach (posts() as $p) {
        $texto = normalizar($p['titulo'] . ' ' . implode(' ', $p['palavras_chave'] ?? []));
        $pontos = count(array_filter($temas, fn($t) => str_contains($texto, $t)));
        if ($pontos > 0) $pontuados[] = [$pontos, $p];
    }
    usort($pontuados, fn($a, $b) => $b[0] <=> $a[0] ?: strcmp($b[1]['publicado_em'], $a[1]['publicado_em']));
    return array_slice(array_column($pontuados, 1), 0, $quantidade);
}

function slug_valido(string $slug): bool
{
    return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug);
}

/** "Gastrite: sintomas de alerta" -> "gastrite-sintomas-de-alerta" */
function gerar_slug(string $texto): string
{
    $s = normalizar($texto);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim((string) $s, '-');
}

/** Grava o artigo (escrita atômica: arquivo temporário + rename). */
function salvar_post(array $post): void
{
    if (!slug_valido($post['slug'] ?? '')) throw new InvalidArgumentException('Endereço (slug) inválido.');
    $json = json_encode($post, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    $destino = pasta_blog() . "/{$post['slug']}.json";
    $tmp = $destino . '.tmp';
    if (file_put_contents($tmp, $json, LOCK_EX) === false || !rename($tmp, $destino)) {
        throw new RuntimeException('Não foi possível gravar o artigo. Verifique a permissão da pasta conteudo/blog.');
    }
}

function excluir_post(string $slug): void
{
    if (!slug_valido($slug)) return;
    $arquivo = pasta_blog() . "/{$slug}.json";
    if (is_file($arquivo)) {
        // Guarda uma cópia na lixeira em vez de apagar de vez
        $lixeira = CONTEUDO . '/lixeira';
        if (!is_dir($lixeira)) mkdir($lixeira, 0755, true);
        rename($arquivo, $lixeira . "/{$slug}-" . date('Ymd-His') . '.json');
    }
}
