<?php
// /exames/ – lista de exames e procedimentos.
$pagina = [
    'titulo' => 'Exames e Procedimentos em Fortaleza | GastriClin',
    'descricao' => 'Endoscopia, colonoscopia, polipectomia e retossigmoidoscopia em Fortaleza, com sedação e no mesmo lugar da consulta. Agende online ou pelo WhatsApp.',
];
require APP . '/layout/inicio.php';

componente('topo-pagina', [
    'trilha' => [['rotulo' => 'Exames', 'href' => '/exames/']],
    'sobretitulo' => 'Exames e procedimentos',
    'titulo_html' => 'Exames feitos <em>na própria clínica</em>',
    'lead' => 'Exames de rotina ajudam a detectar alterações do sistema digestivo cedo, muitas vezes antes dos sintomas. Escolha o exame para saber quando é indicado e agendar.',
    'extra_html' => componente_html('botoes-agendar', ['origem' => 'exames-topo', 'texto_agendar' => 'Agendar exame']),
]);
?>
  <section class="secao">
    <div class="container">
      <div class="grade grade--2">
        <?php foreach (exames() as $s) componente('card-servico', ['servico' => $s, 'origem' => 'exames', 'titulo' => 'h2']); ?>
      </div>
    </div>
  </section>

  <section class="secao secao--branca">
    <div class="container">
      <div class="cabecalho-secao">
        <p class="sobretitulo">Antes do exame</p>
        <h2>Comece por uma <em>consulta</em></h2>
        <p>O especialista avalia seus sintomas e indica o exame certo para o seu caso.</p>
      </div>
      <div class="grade grade--2">
        <?php foreach (consultas() as $s) componente('card-servico', ['servico' => $s, 'origem' => 'exames-consultas']); ?>
      </div>
    </div>
  </section>
<?php require APP . '/layout/fim.php'; ?>
