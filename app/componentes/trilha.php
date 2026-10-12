<?php
// Trilha de navegação (breadcrumb) com dados estruturados BreadcrumbList.
// Variável: $itens = [['rotulo' => 'Exames', 'href' => '/exames/'], ...]
$todos = array_merge([['rotulo' => 'Início', 'href' => '/']], $itens);
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => array_map(fn($i, $n) => [
        '@type' => 'ListItem',
        'position' => $n + 1,
        'name' => $i['rotulo'],
        'item' => SITE['url'] . $i['href'],
    ], $todos, array_keys($todos)),
];
?>
<nav class="trilha" aria-label="Você está em">
  <ol>
    <?php foreach ($todos as $n => $i): ?>
      <li><?php if ($n < count($todos) - 1): ?><a href="<?= e($i['href']) ?>"><?= e($i['rotulo']) ?></a><?php else: ?><span aria-current="page"><?= e($i['rotulo']) ?></span><?php endif; ?></li>
    <?php endforeach; ?>
  </ol>
</nav>
<?= json_ld($schema) ?>
