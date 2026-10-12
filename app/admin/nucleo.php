<?php
// Núcleo do painel /admin/: sessão, usuários, permissões, proteção CSRF e limite de tentativas de login.

declare(strict_types=1);

require_once dirname(__DIR__) . '/funcoes.php';
require_once __DIR__ . '/html_seguro.php';
require_once __DIR__ . '/imagens.php';

// Papéis: o marketing entrega o texto e a equipe de TI publica pelo painel.
// editor = equipe de TI (cria, edita, publica e exclui artigos); admin = tudo + gerencia usuários.
const PAPEIS = [
    'editor' => 'Equipe de TI',
    'admin' => 'Administrador(a)',
];

const ARQUIVO_USUARIOS = CONTEUDO . '/usuarios.json';
const ARQUIVO_TENTATIVAS = CONTEUDO . '/tentativas-login.json';
const MAX_TENTATIVAS = 5;
const BLOQUEIO_MINUTOS = 15;

function iniciar_sessao(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') === '443';
    session_name('gastriclin_admin');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/admin/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Strict']);
    session_start();
    // Expira após 2 horas sem uso
    if (isset($_SESSION['ultimo_acesso']) && time() - $_SESSION['ultimo_acesso'] > 7200) {
        $_SESSION = [];
        session_regenerate_id(true);
    }
    $_SESSION['ultimo_acesso'] = time();
}

// ---------------------------------------------------------------- Usuários

function usuarios(): array
{
    if (!is_file(ARQUIVO_USUARIOS)) return [];
    return json_decode((string) file_get_contents(ARQUIVO_USUARIOS), true) ?: [];
}

function salvar_usuarios(array $usuarios): void
{
    gravar_json(ARQUIVO_USUARIOS, $usuarios);
}

function usuario_atual(): ?array
{
    $email = $_SESSION['usuario'] ?? null;
    if (!$email) return null;
    foreach (usuarios() as $u) if ($u['email'] === $email && ($u['ativo'] ?? true)) return $u;
    return null;
}

function pode(string $acao, ?array $u = null): bool
{
    $u ??= usuario_atual();
    if (!$u) return false;
    return match ($acao) {
        'usuarios' => $u['papel'] === 'admin',
        default => true,
    };
}

function exigir_login(): array
{
    $u = usuario_atual();
    if (!$u) {
        header('Location: /admin/?acao=entrar');
        exit;
    }
    return $u;
}

// ---------------------------------------------------------------- Login com limite de tentativas

function ip_cliente(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
}

function tentativas(): array
{
    if (!is_file(ARQUIVO_TENTATIVAS)) return [];
    $t = json_decode((string) file_get_contents(ARQUIVO_TENTATIVAS), true) ?: [];
    // descarta registros antigos
    return array_filter($t, fn($r) => time() - $r['ultima'] < BLOQUEIO_MINUTOS * 60);
}

function login_bloqueado(): bool
{
    $r = tentativas()[ip_cliente()] ?? null;
    return $r && $r['quantidade'] >= MAX_TENTATIVAS;
}

function registrar_falha_login(): void
{
    $t = tentativas();
    $ip = ip_cliente();
    $t[$ip] = ['quantidade' => ($t[$ip]['quantidade'] ?? 0) + 1, 'ultima' => time()];
    gravar_json(ARQUIVO_TENTATIVAS, $t);
}

function entrar(string $email, string $senha): bool
{
    if (login_bloqueado()) return false;
    foreach (usuarios() as $u) {
        if (strcasecmp($u['email'], $email) === 0 && ($u['ativo'] ?? true) && password_verify($senha, $u['senha'])) {
            session_regenerate_id(true);
            $_SESSION['usuario'] = $u['email'];
            $t = tentativas();
            unset($t[ip_cliente()]);
            gravar_json(ARQUIVO_TENTATIVAS, $t);
            return true;
        }
    }
    password_verify($senha, '$2y$10$GB5xxtVWkgKzjeOjlieVZuV5eRTkftO/QaA0limwCf7H5VU8xUvJu'); // mesmo tempo de resposta com ou sem usuário
    registrar_falha_login();
    return false;
}

function sair(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
    session_destroy();
}

// ---------------------------------------------------------------- CSRF e mensagens

function token_csrf(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function campo_csrf(): string
{
    return '<input type="hidden" name="csrf" value="' . token_csrf() . '">';
}

function verificar_csrf(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Sessão expirada. Volte e tente novamente.');
    }
}

function avisar(string $mensagem, string $tipo = 'ok'): void
{
    $_SESSION['avisos'][] = [$tipo, $mensagem];
}

function pegar_avisos(): array
{
    $a = $_SESSION['avisos'] ?? [];
    unset($_SESSION['avisos']);
    return $a;
}

// ---------------------------------------------------------------- Utilitários

/** Grava JSON com bloqueio de escrita (arquivo temporário + rename). */
function gravar_json(string $arquivo, array $dados): void
{
    $dir = dirname($arquivo);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $tmp = $arquivo . '.tmp';
    $json = json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if (file_put_contents($tmp, $json, LOCK_EX) === false || !rename($tmp, $arquivo)) {
        throw new RuntimeException('Não foi possível gravar ' . basename($arquivo) . '. Verifique as permissões da pasta conteudo/.');
    }
}

/** Pede ao LiteSpeed (Hostinger) para renovar o cache das páginas depois de uma publicação. */
function limpar_cache_site(): void
{
    if (!headers_sent()) header('X-LiteSpeed-Purge: *');
}

function redirecionar_admin(string $destino): never
{
    header('Location: ' . $destino, true, 303);
    exit;
}
