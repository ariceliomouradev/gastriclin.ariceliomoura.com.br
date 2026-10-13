<?php
// /blog/ e /blog/pagina/N/ – lista de artigos publicados, 9 por página, do mais recente para o mais antigo.
const ARTIGOS_POR_PAGINA = 9;

$todos = posts();
$total_paginas = max(1, (int) ceil(count($todos) / ARTIGOS_POR_PAGINA));
$numero_pagina ??= 1;

// Página além da última: 404 (evita páginas vazias indexadas)
if ($numero_pagina > $total_paginas) {
    http_response_code(404);
    require APP . '/paginas/404.php';
    return;
}

$artigos = array_slice($todos, ($numero_pagina - 1) * ARTIGOS_POR_PAGINA, ARTIGOS_POR_PAGINA);
$sufixo = $numero_pagina > 1 ? " – Página {$numero_pagina}" : '';

$pagina = [
    'titulo' => $numero_pagina > 1 ? "Blog de Saúde Digestiva{$sufixo} | GastriClin" : 'Blog de Saúde Digestiva | GastriClin Fortaleza',
    'descricao' => $numero_pagina > 1
        ? "Artigos da GastriClin sobre saúde digestiva, página {$numero_pagina} de {$total_paginas}: gastrite, refluxo, intestino, endoscopia e colonoscopia."
        : 'Artigos sobre gastrite, refluxo, intestino, endoscopia, colonoscopia e cuidados com a saúde digestiva, da equipe da GastriClin em Fortaleza.',
];
require APP . '/layout/inicio.php';

$trilha = [['rotulo' => 'Blog', 'href' => '/blog/']];
if ($numero_pagina > 1) $trilha[] = ['rotulo' => "Página {$numero_pagina}", 'href' => "/blog/pagina/{$numero_pagina}/"];

componente('topo-pagina', [
    'trilha' => $trilha,
    'sobretitulo' => 'Blog',
    'titulo_html' => 'Saúde digestiva <em>sem complicação</em>',
    'lead' => 'Informação clara sobre sintomas, exames e hábitos para cuidar melhor da sua saúde digestiva.',
]);
?>
  <section class="secao" id="artigos">
    <div class="container">
      <div class="grade grade--3">
        <?php foreach ($artigos as $p) componente('card-post', ['post' => $p, 'titulo' => 'h2']); ?>
      </div>
      <?php componente('paginacao', ['atual' => $numero_pagina, 'total' => $total_paginas, 'url' => fn(int $n) => $n === 1 ? '/blog/' : "/blog/pagina/{$n}/"]); ?>
    </div>
  </section>
<?php require APP . '/layout/fim.php'; ?>
