<?php
// /especialidades/ – lista de consultas.
$pagina = [
    'titulo' => 'Especialidades e Consultas em Fortaleza | GastriClin',
    'descricao' => 'Consultas em gastroenterologia e coloproctologia em Fortaleza para diagnóstico e tratamento das doenças digestivas. Agende online ou pelo WhatsApp.',
];
require APP . '/layout/inicio.php';

componente('topo-pagina', [
    'trilha' => [['rotulo' => 'Consultas', 'href' => '/especialidades/']],
    'sobretitulo' => 'Consultas',
    'titulo_html' => 'Consultas com <em>especialistas</em>',
    'lead' => 'Escolha a especialidade para ver quando procurar, quem atende e agendar sua consulta.',
    'extra_html' => componente_html('botoes-agendar', ['origem' => 'especialidades-topo', 'texto_agendar' => 'Agendar consulta']),
]);
?>
  <section class="secao">
    <div class="container">
      <div class="grade grade--2">
        <?php foreach (consultas() as $s) componente('card-servico', ['servico' => $s, 'origem' => 'especialidades', 'titulo' => 'h2']); ?>
      </div>
    </div>
  </section>

  <section class="secao secao--branca">
    <div class="container">
      <div class="cabecalho-secao">
        <p class="sobretitulo">Exames na própria clínica</p>
        <h2>Precisa de um <em>exame</em>?</h2>
        <p>Se o médico pedir endoscopia ou colonoscopia, você faz aqui mesmo.</p>
      </div>
      <div class="grade grade--4">
        <?php foreach (exames() as $s) componente('card-servico', ['servico' => $s, 'origem' => 'especialidades-exames']); ?>
      </div>
    </div>
  </section>
<?php require APP . '/layout/fim.php'; ?>
