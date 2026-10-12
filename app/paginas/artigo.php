<?php
// Artigo do blog (/<slug>/). Recebe $post do roteador.
$relacionados = posts_relacionados($post);
$capa = $post['capa'] ?? null;
$revisao = $post['revisao_medica'] ?? [];

$pagina = [
    'titulo' => titulo_com_marca($post['titulo_seo'] ?: $post['titulo']),
    'descricao' => limitar($post['descricao']),
    'tipo' => 'article',
    'imagem' => $capa ? '/' . $capa['arquivo'] . '-' . max($capa['larguras']) . '.webp' : '/og-gastriclin.jpg',
    'json_ld' => [array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post['titulo_seo'] ?: $post['titulo'],
        'description' => $post['descricao'],
        'datePublished' => $post['publicado_em'],
        'dateModified' => $post['atualizado_em'] ?: $post['publicado_em'],
        'image' => $capa ? SITE['url'] . '/' . $capa['arquivo'] . '-' . max($capa['larguras']) . '.webp' : null,
        'author' => ['@type' => 'Organization', 'name' => SITE['nome'], 'url' => SITE['url']],
        'publisher' => ['@id' => SITE['url'] . '/#clinica'],
        'mainEntityOfPage' => SITE['url'] . "/{$post['slug']}/",
        'keywords' => implode(', ', $post['palavras_chave'] ?? []),
    ])],
];
require APP . '/layout/inicio.php';
?>
  <article>
    <header class="artigo-topo">
      <div class="container artigo-topo__conteudo">
        <?php componente('trilha', ['itens' => [['rotulo' => 'Blog', 'href' => '/blog/'], ['rotulo' => $post['titulo'], 'href' => "/{$post['slug']}/"]]]); ?>
        <p class="sobretitulo"><?= e($post['categorias'][0] ?? 'Saúde digestiva') ?></p>
        <h1><?= e($post['titulo']) ?></h1>
        <p class="artigo-topo__meta">
          <time datetime="<?= e(substr($post['publicado_em'], 0, 10)) ?>"><?= data_extenso($post['publicado_em']) ?></time>
          <?php if (!empty($revisao['revisado']) && !empty($revisao['por'])): ?><span> · Revisado por <?= e($revisao['por']) ?></span><?php endif; ?>
        </p>
      </div>
      <?php if ($capa): ?>
        <div class="container">
          <?= imagem_capa($capa, $capa['alt'] ?: $post['titulo'], ['classe' => 'artigo-topo__img', 'sizes' => '(max-width: 1000px) 92vw, 900px', 'carregar' => 'eager']) ?>
        </div>
      <?php endif; ?>
    </header>

    <div class="container artigo-corpo">
      <div class="prosa">
        <?= $post['conteudo_html'] /* HTML já limpo pelo painel ao salvar */ ?>
        <p class="aviso">
          Conteúdo informativo. Não substitui a consulta médica. Em caso de sintomas intensos, sangramento ou dor forte, procure atendimento de urgência.
        </p>
      </div>

      <aside class="cartao-cta">
        <p class="sobretitulo">GastriClin</p>
        <p class="cartao-cta__titulo">Quer conversar com um especialista?</p>
        <p>Agende sua consulta ou exame em Fortaleza.</p>
        <?php componente('botoes-agendar', ['origem' => "blog-{$post['slug']}", 'tamanho' => 'pequeno']); ?>
      </aside>
    </div>
  </article>

  <?php if ($relacionados): ?>
    <section class="secao secao--branca">
      <div class="container">
        <div class="cabecalho-secao">
          <p class="sobretitulo">Continue lendo</p>
          <h2>Artigos <em>relacionados</em></h2>
        </div>
        <div class="grade grade--3">
          <?php foreach ($relacionados as $r) componente('card-post', ['post' => $r]); ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
<?php require APP . '/layout/fim.php'; ?>
