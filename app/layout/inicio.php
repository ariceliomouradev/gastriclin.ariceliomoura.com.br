<?php
// Início de toda página: <head> com SEO, dados estruturados, CSS embutido e cabeçalho.
// A página define $pagina antes de incluir este arquivo:
//   titulo, descricao (obrigatórios); imagem, tipo ('website'|'article'|'profile'), json_ld (lista),
//   servico (mensagem do WhatsApp), noindex (bool)
$pagina += ['imagem' => '/og-gastriclin.jpg', 'tipo' => 'website', 'json_ld' => [], 'servico' => null, 'noindex' => false];
$canonical = SITE['url'] . caminho_atual();
$imagem_og = str_starts_with($pagina['imagem'], 'http') ? $pagina['imagem'] : SITE['url'] . $pagina['imagem'];
$end = SITE['endereco'];

// Dados estruturados da clínica em todas as páginas (Google: MedicalClinic / negócio local)
$clinica = [
    '@context' => 'https://schema.org',
    '@type' => 'MedicalClinic',
    '@id' => SITE['url'] . '/#clinica',
    'name' => SITE['nome'],
    'alternateName' => SITE['nome_completo'],
    'url' => SITE['url'] . '/',
    'logo' => SITE['url'] . '/marca/simbolo.png',
    'image' => SITE['url'] . '/og-gastriclin.jpg',
    'telephone' => SITE['telefone']['e164'],
    'email' => SITE['email'],
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => $end['rua'] . ', ' . $end['complemento'],
        'addressLocality' => $end['cidade'],
        'addressRegion' => $end['uf'],
        'postalCode' => $end['cep'],
        'addressCountry' => 'BR',
    ],
    'hasMap' => SITE['links']['google_maps'],
    'medicalSpecialty' => 'Gastroenterologic',
    'availableService' => array_map(fn($s) => [
        '@type' => $s['tipo'] === 'exame' ? 'MedicalProcedure' : 'MedicalTherapy',
        'name' => $s['nome'],
        'url' => SITE['url'] . "/{$s['slug']}/",
    ], servicos()),
    'sameAs' => [SITE['links']['instagram'], SITE['links']['facebook'], SITE['links']['doctoralia'], SITE['links']['google_perfil']],
];
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pagina['titulo']) ?></title>
    <meta name="description" content="<?= e($pagina['descricao']) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <?php if ($pagina['noindex']): ?><meta name="robots" content="noindex, follow"><?php endif; ?>

    <meta property="og:locale" content="pt_BR">
    <meta property="og:type" content="<?= e($pagina['tipo']) ?>">
    <meta property="og:site_name" content="<?= e(SITE['nome']) ?>">
    <meta property="og:title" content="<?= e($pagina['titulo']) ?>">
    <meta property="og:description" content="<?= e($pagina['descricao']) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:image" content="<?= e($imagem_og) ?>">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="preload" href="/assets/fonts/inter-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/assets/fonts/fraunces-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/assets/fonts/fraunces-latin-wght-italic.woff2" as="font" type="font/woff2" crossorigin>

    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <!-- Cor da barra do navegador no celular: roxo profundo da marca -->
    <meta name="theme-color" content="#382B61">
    <meta name="msapplication-TileColor" content="#382B61">
    <link rel="sitemap" href="/sitemap.xml">

    <style><?= css_embutido() ?></style>

    <?= json_ld($clinica) ?>
    <?php foreach ($pagina['json_ld'] as $schema) echo json_ld($schema); ?>
  </head>
  <body data-ga4="<?= e(SITE['ga4_id']) ?>">
    <?php readfile(APP . '/componentes/icones.svg'); ?>
    <a href="#conteudo" class="pular-link">Pular para o conteúdo</a>
    <?php componente('header'); ?>
    <main id="conteudo">
