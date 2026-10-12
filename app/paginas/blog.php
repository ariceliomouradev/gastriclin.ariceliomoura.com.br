<?php
// /blog/ – lista de artigos publicados.
$pagina = [
    'titulo' => 'Blog de Saúde Digestiva | GastriClin Fortaleza',
    'descricao' => 'Artigos sobre gastrite, refluxo, intestino, endoscopia, colonoscopia e cuidados com a saúde digestiva, da equipe da GastriClin em Fortaleza.',
];
require APP . '/layout/inicio.php';

componente('topo-pagina', [
    'trilha' => [['rotulo' => 'Blog', 'href' => '/blog/']],
    'sobretitulo' => 'Blog',
    'titulo_html' => 'Saúde digestiva <em>sem complicação</em>',
    'lead' => 'Informação clara sobre sintomas, exames e hábitos para cuidar melhor da sua saúde digestiva.',
]);
?>
  <section class="secao">
    <div class="container">
      <div class="grade grade--3">
        <?php foreach (posts() as $p) componente('card-post', ['post' => $p, 'titulo' => 'h2']); ?>
      </div>
    </div>
  </section>
<?php require APP . '/layout/fim.php'; ?>
