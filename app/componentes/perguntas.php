<?php
// Perguntas frequentes com <details> (sanfona, funciona sem JavaScript) e dados estruturados FAQPage.
// Variável: $perguntas = [['pergunta' => '...', 'resposta' => '<p>HTML</p>'], ...]
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(fn($p) => [
        '@type' => 'Question',
        'name' => $p['pergunta'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($p['resposta'])],
    ], $perguntas),
];
?>
<div class="perguntas">
  <?php foreach ($perguntas as $p): ?>
    <details class="pergunta">
      <summary>
        <span><?= e($p['pergunta']) ?></span>
        <span class="pergunta__sinal" aria-hidden="true"></span>
      </summary>
      <div class="pergunta__resposta"><?= $p['resposta'] ?></div>
    </details>
  <?php endforeach; ?>
</div>
<?= json_ld($schema) ?>
