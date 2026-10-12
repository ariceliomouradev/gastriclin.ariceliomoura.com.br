<?php
// /sobre/ – a clínica.
$fotos = [
    ['clinica/fachada', 'Fachada do prédio onde fica a GastriClin'],
    ['clinica/recepcao-2', 'Sala de espera'],
    ['clinica/consultorio', 'Consultório'],
    ['clinica/sala-exame', 'Sala de exames'],
    ['clinica/sala-recuperacao', 'Sala de recuperação'],
];
$pagina = [
    'titulo' => 'Sobre a GastriClin | Gastroenterologia em Fortaleza',
    'descricao' => 'Conheça a GastriClin, centro de medicina digestiva em Fortaleza: consultas e exames com atendimento humanizado, ético e estrutura própria no bairro de Fátima.',
];
require APP . '/layout/inicio.php';

componente('topo-pagina', [
    'trilha' => [['rotulo' => 'A clínica', 'href' => '/sobre/']],
    'sobretitulo' => 'A clínica',
    'titulo_html' => 'Cuidar de pessoas sempre foi <em>o nosso propósito</em>',
    'lead' => 'A GastriClin nasceu do compromisso de oferecer um atendimento humanizado, ético e de excelência em saúde digestiva.',
]);
?>
  <section class="secao secao--branca">
    <div class="container historia">
      <div class="historia__foto">
        <?= imagem('clinica/fundadores', 'Valdeline Sousa e Dr. Lucas Mapurunga, sócios-fundadores da GastriClin', ['larguras' => [400, 640, 960], 'sizes' => '(max-width: 900px) 92vw, 460px', 'carregar' => 'eager']) ?>
      </div>
      <div class="prosa">
        <p class="sobretitulo">Quem somos</p>
        <h2>Experiência assistencial e de gestão <em>a serviço do paciente</em></h2>
        <p>
          À frente da GastriClin estão o <a href="/dr-lucas-dourado-mapurunga-pereira/">Dr. Lucas Mapurunga</a>, médico clínico e endoscopista digestivo, e
          <a href="/valdeline-sousa/">Valdeline Sousa</a>, enfermeira, gestora em saúde e auditora. Juntos, unem experiência assistencial e de gestão para oferecer
          uma clínica moderna, acolhedora e focada na qualidade do cuidado.
        </p>
        <p>
          Mais do que realizar consultas e exames, acreditamos que cada paciente merece ser acolhido com respeito, atenção e segurança em todas as etapas do
          atendimento.
        </p>
      </div>
    </div>
  </section>

  <section class="secao">
    <div class="container">
      <div class="cabecalho-secao">
        <p class="sobretitulo">Nosso diferencial</p>
        <h2>Conhecimento técnico, empatia e um <em>ambiente planejado</em></h2>
      </div>
      <ul class="diferenciais">
        <li><?= icone('estetoscopio', 28) ?><h3>Profissionais especializados</h3><p>Gastroenterologia, coloproctologia e endoscopia digestiva.</p></li>
        <li><?= icone('escudo', 28) ?><h3>Exames com cuidado</h3><p>Endoscopia e colonoscopia com sedação, conforto e segurança.</p></li>
        <li><?= icone('usuarios', 28) ?><h3>Atendimento humanizado</h3><p>Suporte da recepção ao pós-atendimento, com informação clara.</p></li>
        <li><?= icone('check', 28) ?><h3>Prevenção</h3><p>Foco em prevenção, diagnóstico precoce e qualidade de vida.</p></li>
      </ul>
    </div>
  </section>

  <section class="secao secao--roxa">
    <div class="container mvv">
      <div>
        <h2>Missão</h2>
        <p>Fornecer cuidados de saúde com qualidade, abrangentes e acessíveis, colocando o bem-estar e a satisfação dos nossos pacientes em primeiro lugar.</p>
      </div>
      <div>
        <h2>Visão</h2>
        <p>Ser reconhecida como clínica médica de referência pela qualidade dos serviços, pela inovação e pela abordagem humanizada.</p>
      </div>
      <div>
        <h2>Valores</h2>
        <p>Comprometimento, credibilidade, qualidade, empatia, acessibilidade e responsabilidade social.</p>
      </div>
    </div>
  </section>

  <section class="secao secao--branca">
    <div class="container">
      <div class="cabecalho-secao">
        <p class="sobretitulo">Estrutura</p>
        <h2>Conheça <em>nossos espaços</em></h2>
        <p>Estrutura moderna, climatizada e acessível, com salas de exame e de recuperação na própria clínica.</p>
      </div>
      <div class="galeria">
        <?php foreach ($fotos as $i => [$chave, $alt]): ?>
          <?= imagem($chave, $alt, ['classe' => 'galeria__item galeria__item--' . ($i + 1), 'larguras' => [400, 640, 960], 'sizes' => '(max-width: 760px) 92vw, 40vw']) ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="secao">
    <div class="container centro">
      <h2>Venha nos <em>conhecer</em></h2>
      <p>Agende sua consulta ou exame.</p>
      <?php componente('botoes-agendar', ['origem' => 'sobre-fim', 'classe' => 'centro__botoes']); ?>
    </div>
  </section>
<?php require APP . '/layout/fim.php'; ?>
