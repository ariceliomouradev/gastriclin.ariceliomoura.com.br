<?php
// /clinico/ – corpo clínico e gestão.
$gestao = array_values(array_filter(profissionais_ativos(), fn($p) => !$p['medico']));
$pagina = [
    'titulo' => 'Corpo Clínico | GastriClin Fortaleza',
    'descricao' => 'Conheça os médicos da GastriClin em Fortaleza: gastroenterologia, coloproctologia e endoscopia digestiva. Veja formação, registro e agende sua consulta.',
];
require APP . '/layout/inicio.php';

componente('topo-pagina', [
    'trilha' => [['rotulo' => 'Corpo clínico', 'href' => '/clinico/']],
    'sobretitulo' => 'Corpo clínico',
    'titulo_html' => 'Nossa <em>equipe</em>',
    'lead' => 'Médicos com formação nas principais instituições do Ceará, unidos pelo mesmo objetivo: um atendimento de qualidade, com segurança, acolhimento e respeito.',
    'extra_html' => componente_html('botoes-agendar', ['origem' => 'clinico-topo']),
]);
?>
  <section class="secao">
    <div class="container">
      <div class="cabecalho-secao">
        <p class="sobretitulo">Médicos</p>
        <h2>Escolha o <em>especialista</em></h2>
        <p>Clique no perfil para ver formação, áreas de atuação e registro profissional.</p>
      </div>
      <div class="grade grade--medicos">
        <?php foreach (medicos_ativos() as $m) componente('card-medico', ['pessoa' => $m]); ?>
      </div>
    </div>
  </section>

  <section class="secao secao--branca">
    <div class="container equipe">
      <div class="equipe__foto">
        <?= imagem('clinica/equipe', 'Equipe da GastriClin reunida na clínica', ['larguras' => [400, 640, 960], 'sizes' => '(max-width: 900px) 92vw, 460px']) ?>
      </div>
      <div>
        <p class="sobretitulo">Gestão e equipe</p>
        <h2>Cuidado que começa <em>na recepção</em></h2>
        <p>
          Na GastriClin, os melhores resultados vêm do trabalho conjunto de médicos, enfermagem, recepção e gestão, com processos organizados e foco na
          segurança do paciente.
        </p>
        <div class="grade grade--medicos gestao">
          <?php foreach ($gestao as $p) componente('card-medico', ['pessoa' => $p]); ?>
        </div>
      </div>
    </div>
  </section>
<?php require APP . '/layout/fim.php'; ?>
