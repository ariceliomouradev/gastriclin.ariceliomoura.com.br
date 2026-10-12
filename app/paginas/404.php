<?php
// Página não encontrada.
$pagina = [
    'titulo' => 'Página não encontrada | GastriClin',
    'descricao' => 'A página que você procurou não existe.',
    'noindex' => true,
];
require APP . '/layout/inicio.php';
?>
  <section class="secao">
    <div class="container">
      <div class="cabecalho-secao cabecalho-secao--centro">
        <p class="sobretitulo">Erro 404</p>
        <h1>Esta página <em>não existe</em> mais</h1>
        <p>O site da GastriClin mudou. Veja abaixo o que você pode estar procurando ou agende direto.</p>
        <?php componente('botoes-agendar', ['origem' => '404', 'classe' => 'centro-404']); ?>
      </div>
      <div class="grade grade--3">
        <?php foreach (servicos() as $s) componente('card-servico', ['servico' => $s, 'origem' => '404', 'titulo' => 'h2']); ?>
      </div>
    </div>
  </section>
<?php require APP . '/layout/fim.php'; ?>
