<?php
// Card de artigo do blog. Variáveis: $post (array), $titulo ('h3' | 'h2')
$titulo ??= 'h3';
?>
<article class="card-post">
  <?php if (!empty($post['capa'])): ?>
    <div class="card-post__img">
      <?= imagem_capa($post['capa'], '', ['larguras' => [360, 540, 720], 'sizes' => '(max-width: 700px) 92vw, 380px']) ?>
    </div>
  <?php endif; ?>
  <div class="card-post__corpo">
    <time datetime="<?= e(substr($post['publicado_em'], 0, 10)) ?>"><?= data_extenso($post['publicado_em']) ?></time>
    <<?= $titulo ?> class="card-post__titulo"><a href="/<?= $post['slug'] ?>/"><?= e($post['titulo']) ?></a></<?= $titulo ?>>
  </div>
</article>
