<?php
// Ponto de entrada do site: recebe todas as URLs (via .htaccess) e entrega a página certa.
// URLs iguais às do site antigo (WordPress), sempre com barra no final.

declare(strict_types=1);

// Servidor embutido do PHP (teste local: php -S): entrega os arquivos reais.
// Fontes, CSS, JS e imagens saem com cache, como na Hostinger (.htaccess), para o teste refletir a produção.
if (PHP_SAPI === 'cli-server') {
    // Mesmo bloqueio do .htaccess: pastas internas, arquivos ocultos e de documentação
    $pedido = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    if (preg_match('#^/(app|conteudo|dados-iniciais|ferramentas|originais)(/|$)|/\.(?!well-known/)|\.(md|py|mjs|sh|yml|yaml|lock)$#', $pedido)) {
        http_response_code(403);
        exit('Acesso negado.');
    }
    $real = __DIR__ . (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
    if ($real !== __DIR__ . '/' && is_file(rtrim($real, '/') . '/index.php')) return false;
    if ($real !== __DIR__ . '/' && is_file($real)) {
        $tipos = ['woff2' => 'font/woff2', 'webp' => 'image/webp', 'css' => 'text/css', 'js' => 'text/javascript', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'svg' => 'image/svg+xml'];
        $ext = strtolower(pathinfo($real, PATHINFO_EXTENSION));
        if (!isset($tipos[$ext])) return false;
        header('Content-Type: ' . $tipos[$ext]);
        header('Cache-Control: public, max-age=86400');
        readfile($real);
        return true;
    }
}

// Esta pasta é a raiz do site. app/, conteudo/ e as demais pastas internas são bloqueadas pelo .htaccess.
define('PUBLICO', __DIR__);
require __DIR__ . '/app/funcoes.php';

// Compressão gzip do HTML (se o servidor já comprime, o PHP não comprime de novo)
if (!ini_get('zlib.output_compression') && str_contains($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip')) {
    ob_start('ob_gzhandler');
}

$caminho = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$query = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '' ? '?' . $_SERVER['QUERY_STRING'] : '';

function redirecionar(string $destino, int $codigo = 301): never
{
    header('Location: ' . $destino, true, $codigo);
    exit;
}

// Sitemap gerado na hora (inclui os artigos publicados pelo painel)
if ($caminho === '/sitemap.xml' || $caminho === '/sitemap-index.xml') {
    require APP . '/paginas/sitemap.php';
    exit;
}

// Barra no final, como no site antigo: /endoscopia -> /endoscopia/
if (!str_ends_with($caminho, '/') && !preg_match('/\.[a-z0-9]{2,5}$/i', $caminho)) {
    redirecionar($caminho . '/' . $query);
}

// Redirecionamentos de URLs antigas
if (isset(REDIRECIONAMENTOS[$caminho])) {
    [$destino, $codigo] = REDIRECIONAMENTOS[$caminho];
    redirecionar($destino, $codigo);
}
foreach (REDIRECIONAMENTOS_PREFIXO as $prefixo) {
    if (str_starts_with($caminho, $prefixo)) redirecionar('/blog/');
}

// Páginas fixas
$rotas = [
    '/' => 'inicio',
    '/especialidades/' => 'especialidades',
    '/exames/' => 'exames',
    '/clinico/' => 'clinico',
    '/convenios/' => 'convenios',
    '/sobre/' => 'sobre',
    '/agendar/' => 'agendar',
    '/blog/' => 'blog',
    '/bio/' => 'bio',
    '/privacidade/' => 'privacidade',
];
if (isset($rotas[$caminho])) {
    require APP . "/paginas/{$rotas[$caminho]}.php";
    exit;
}

// Blog paginado: /blog/pagina/2/, /blog/pagina/3/ ... (a página 1 é sempre /blog/)
if (preg_match('#^/blog/pagina/(\d+)/$#', $caminho, $m)) {
    $numero_pagina = (int) $m[1];
    if ($numero_pagina <= 1) redirecionar('/blog/');
    require APP . '/paginas/blog.php';
    exit;
}

// Páginas na raiz do domínio: serviços (/endoscopia/), perfis (/dr-.../) e artigos do blog (/<slug>/)
$slug = trim($caminho, '/');
if (slug_valido($slug)) {
    if ($servico = servico($slug)) {
        require APP . '/paginas/servico.php';
        exit;
    }
    if ($pessoa = profissional($slug)) {
        require APP . '/paginas/perfil.php';
        exit;
    }
    if ($post = post($slug)) {
        require APP . '/paginas/artigo.php';
        exit;
    }
}

http_response_code(404);
require APP . '/paginas/404.php';
