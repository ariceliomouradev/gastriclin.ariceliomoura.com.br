<?php
// Página inicial.
$medicos = medicos_ativos();
$posts_recentes = array_slice(posts(), 0, 3);
$total_convenios = count(convenios());
$end = SITE['endereco'];

$perguntas = [
    [
        'pergunta' => 'Como faço para agendar?',
        'resposta' => '<p>Você pode agendar de duas formas: <strong>online</strong>, pela agenda da Doctoralia, escolhendo a especialidade, o médico e o horário; ou pelo <strong>WhatsApp</strong> ' . e(SITE['telefone']['exibicao']) . ', falando com o nosso atendimento.</p>',
    ],
    [
        'pergunta' => 'Vocês atendem pelo meu convênio?',
        'resposta' => "<p>Atendemos {$total_convenios} convênios, entre eles Amil, Bradesco Saúde, SulAmérica, Cassi, GEAP, Issec e Saúde Caixa. Veja a <a href=\"/convenios/\">lista completa</a>. Para confirmar a cobertura do seu plano para consulta ou exame, fale com a gente pelo WhatsApp.</p>",
    ],
    [
        'pergunta' => 'Os exames são feitos com sedação?',
        'resposta' => '<p>Sim. A endoscopia digestiva alta, a colonoscopia e a polipectomia são realizadas com sedação, com acompanhamento da equipe do início ao fim.</p>',
    ],
    [
        'pergunta' => 'Posso fazer a consulta e o exame no mesmo lugar?',
        'resposta' => '<p>Sim. Na GastriClin você faz a consulta com o especialista e os exames de endoscopia e colonoscopia na própria clínica.</p>',
    ],
    [
        'pergunta' => 'Como é o preparo para os exames?',
        'resposta' => '<p>O preparo depende do exame e é orientado pela nossa equipe no momento do agendamento. Se tiver qualquer dúvida, fale com a gente pelo WhatsApp.</p>',
    ],
    [
        'pergunta' => 'Onde fica a GastriClin?',
        'resposta' => '<p>' . e("{$end['rua']}, {$end['complemento']}, bairro {$end['bairro']}, {$end['cidade']} – {$end['uf']}") . '. <a href="' . e(SITE['links']['google_maps']) . '" target="_blank" rel="noopener">Abrir no Google Maps</a>.</p>',
    ],
];

$pagina = [
    'titulo' => 'GastriClin – Gastroenterologia e Endoscopia em Fortaleza',
    'descricao' => 'Consultas em gastroenterologia e coloproctologia, endoscopia e colonoscopia com sedação em Fortaleza. Atendemos convênios. Agende online ou pelo WhatsApp.',
];
require APP . '/layout/inicio.php';
?>
  <!-- ============ TOPO ============ -->
  <section class="hero fundo-escuro">
    <div class="hero__marca-dagua" aria-hidden="true"></div>
    <div class="container hero__grade">
      <div class="hero__texto">
        <p class="sobretitulo">Centro de Medicina Digestiva · Fortaleza</p>
        <h1>Cuidado especializado para a sua <em>saúde digestiva</em></h1>
        <p class="hero__lead">
          Consultas em gastroenterologia e coloproctologia, endoscopia e colonoscopia com sedação. Tudo no mesmo lugar, no bairro de Fátima.
        </p>
        <?php componente('botoes-agendar', ['origem' => 'home-hero']); ?>
        <ul class="hero__selos">
          <li><?= icone('check', 18) ?> Consulta e exame no mesmo lugar</li>
          <li><?= icone('check', 18) ?> Exames com sedação</li>
          <li><?= icone('check', 18) ?> <?= $total_convenios ?> convênios aceitos</li>
        </ul>
      </div>

      <div class="hero__visual">
        <div class="hero__foto">
          <?= imagem('clinica/recepcao', 'Recepção da GastriClin, com cadeiras e balcão de atendimento', ['larguras' => [400, 720, 960], 'sizes' => '(max-width: 900px) 90vw, 520px', 'carregar' => 'eager', 'prioridade' => true]) ?>
        </div>
        <div class="hero__flutuante hero__flutuante--1">
          <span class="hero__flutuante-icone"><?= icone('escudo', 22) ?></span>
          <span><strong>Endoscopia e colonoscopia</strong> com sedação e segurança</span>
        </div>
        <a class="hero__flutuante hero__flutuante--2" href="/clinico/">
          <span class="hero__avatares" aria-hidden="true">
            <?php foreach (array_slice($medicos, 0, 3) as $m) echo imagem($m['foto'], '', ['largura' => 44]); ?>
          </span>
          <span><strong><?= count($medicos) ?> médicos</strong> especialistas</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ============ O QUE VOCÊ PROCURA ============ -->
  <section class="secao" id="servicos" aria-labelledby="titulo-servicos">
    <div class="container">
      <div class="cabecalho-secao cabecalho-secao--centro">
        <p class="sobretitulo">Como podemos ajudar</p>
        <h2 id="titulo-servicos">O que você <em>procura</em>?</h2>
        <p>Escolha a consulta ou o exame. Em cada card você pode agendar direto ou conhecer os detalhes.</p>
      </div>

      <div class="grupo-servicos">
        <h3 class="grupo-servicos__titulo"><?= icone('estetoscopio', 22) ?> Consultas com especialistas</h3>
        <div class="grade grade--2">
          <?php foreach (consultas() as $s) componente('card-servico', ['servico' => $s, 'origem' => 'home-cards']); ?>
        </div>
      </div>

      <div class="grupo-servicos">
        <h3 class="grupo-servicos__titulo"><?= icone('endoscopio', 22) ?> Exames e procedimentos</h3>
        <div class="grade grade--4">
          <?php foreach (exames() as $s) componente('card-servico', ['servico' => $s, 'origem' => 'home-cards']); ?>
        </div>
        <?php componente('card-laudo'); ?>
      </div>

      <p class="ajuda-whats">
        Não sabe qual especialista procurar?
        <a href="<?= e(whatsapp_url('Olá! Vim pelo site da GastriClin e gostaria de ajuda para saber qual atendimento preciso.')) ?>" target="_blank" rel="noopener" data-cta="whatsapp" data-origem="home-ajuda">
          Fale com a gente pelo WhatsApp <?= icone('seta', 18) ?>
        </a>
      </p>
    </div>
  </section>

  <!-- ============ COMO AGENDAR ============ -->
  <section class="secao secao--roxa" aria-labelledby="titulo-passos">
    <div class="container">
      <div class="cabecalho-secao">
        <p class="sobretitulo">Simples e rápido</p>
        <h2 id="titulo-passos">Agende em <em>poucos cliques</em></h2>
      </div>
      <?php componente('passos-agendar'); ?>
    </div>
  </section>

  <!-- ============ CONVÊNIOS ============ -->
  <section class="secao secao--branca" aria-labelledby="titulo-convenios">
    <div class="container">
      <div class="linha-titulo">
        <div class="cabecalho-secao">
          <p class="sobretitulo">Convênios</p>
          <h2 id="titulo-convenios">Atendemos o <em>seu plano</em></h2>
          <p>Consultas e exames pelos principais convênios de Fortaleza. Não encontrou o seu? Pergunte pelo WhatsApp.</p>
        </div>
        <a href="/convenios/" class="link-seta">Ver todos os convênios <?= icone('seta') ?></a>
      </div>
      <?php componente('grade-convenios', ['compacto' => true]); ?>
    </div>
  </section>

  <!-- ============ CORPO CLÍNICO ============ -->
  <section class="secao" aria-labelledby="titulo-equipe">
    <div class="container">
      <div class="linha-titulo">
        <div class="cabecalho-secao">
          <p class="sobretitulo">Corpo clínico</p>
          <h2 id="titulo-equipe">Especialistas que <em>cuidam de você</em></h2>
          <p>Gastroenterologia, coloproctologia e endoscopia digestiva com médicos formados nas principais instituições do Ceará.</p>
        </div>
        <a href="/clinico/" class="link-seta">Conheça a equipe <?= icone('seta') ?></a>
      </div>
      <div class="grade grade--medicos">
        <?php foreach ($medicos as $m) componente('card-medico', ['pessoa' => $m]); ?>
      </div>
    </div>
  </section>

  <!-- ============ ESTRUTURA ============ -->
  <section class="secao secao--branca" aria-labelledby="titulo-estrutura">
    <div class="container estrutura">
      <div class="estrutura__texto">
        <p class="sobretitulo">Nossa estrutura</p>
        <h2 id="titulo-estrutura">Um ambiente pensado para o seu <em>conforto</em></h2>
        <p>
          Na GastriClin, o ambiente faz parte do cuidado. A clínica foi planejada para oferecer conforto, acolhimento e segurança da recepção às salas de exame, com
          estrutura climatizada e equipamentos para endoscopia e colonoscopia no próprio local.
        </p>
        <ul class="lista-check">
          <li><?= icone('check', 20) ?> Salas de exame e de recuperação próprias</li>
          <li><?= icone('check', 20) ?> Ambiente climatizado e acessível</li>
          <li><?= icone('check', 20) ?> Acompanhamento em todas as etapas</li>
        </ul>
        <a href="/sobre/" class="link-seta">Conheça a clínica <?= icone('seta') ?></a>
      </div>
      <div class="mosaico">
        <?= imagem('clinica/recepcao-2', 'Sala de espera da GastriClin', ['classe' => 'mosaico__a', 'larguras' => [400, 640], 'sizes' => '(max-width: 900px) 60vw, 360px']) ?>
        <?= imagem('clinica/consultorio', 'Consultório médico da GastriClin', ['classe' => 'mosaico__b', 'larguras' => [400, 640], 'sizes' => '(max-width: 900px) 40vw, 260px']) ?>
        <?= imagem('clinica/sala-exame', 'Sala de exames de endoscopia e colonoscopia', ['classe' => 'mosaico__c', 'larguras' => [400, 640], 'sizes' => '(max-width: 900px) 40vw, 260px']) ?>
        <?= imagem('clinica/sala-recuperacao', 'Sala de recuperação após os exames', ['classe' => 'mosaico__d', 'larguras' => [400, 640], 'sizes' => '(max-width: 900px) 60vw, 360px']) ?>
      </div>
    </div>
  </section>

  <!-- ============ AVALIAÇÕES ============ -->
  <section class="secao avaliacoes" aria-labelledby="titulo-avaliacoes">
    <div class="container">
      <div class="cabecalho-secao cabecalho-secao--centro">
        <p class="sobretitulo">Avaliações</p>
        <h2 id="titulo-avaliacoes">Veja o que dizem os <em>nossos pacientes</em></h2>
        <p>Avaliações públicas e verificadas, direto da fonte.</p>
      </div>
      <div class="grade grade--2 avaliacoes__grade">
        <?php foreach ([
            [SITE['links']['doctoralia'], 'Opiniões na Doctoralia', 'Avaliações de consultas e exames verificados'],
            [SITE['links']['google_perfil'], 'Avaliações no Google', 'O que os pacientes contam sobre a GastriClin'],
        ] as [$href, $titulo, $texto]): ?>
          <a class="avaliacao" href="<?= e($href) ?>" target="_blank" rel="noopener">
            <span class="avaliacao__estrelas" aria-hidden="true"><?= str_repeat(icone('estrela', 20), 5) ?></span>
            <strong><?= e($titulo) ?></strong>
            <span><?= e($texto) ?> <?= icone('seta-externa', 16) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============ LOCALIZAÇÃO ============ -->
  <section class="secao secao--branca" aria-labelledby="titulo-local">
    <div class="container local">
      <div class="local__foto">
        <?= imagem('clinica/fachada', 'Fachada do prédio da Prontoclínica de Fortaleza, onde fica a GastriClin', ['larguras' => [400, 640, 960], 'sizes' => '(max-width: 900px) 92vw, 560px']) ?>
      </div>
      <div class="local__texto">
        <p class="sobretitulo">Como chegar</p>
        <h2 id="titulo-local">No coração do bairro de <em>Fátima</em></h2>
        <p class="local__endereco">
          <?= icone('local', 22) ?>
          <span>
            <?= e($end['rua']) ?><br>
            <?= e($end['complemento']) ?><br>
            <?= e("{$end['bairro']}, {$end['cidade']} – {$end['uf']} · CEP {$end['cep']}") ?>
          </span>
        </p>
        <div class="local__botoes">
          <a class="btn btn--agendar" href="<?= e(SITE['links']['google_maps']) ?>" target="_blank" rel="noopener"><?= icone('local') ?> Abrir no Google Maps</a>
          <a class="btn btn--contorno" href="<?= e(SITE['links']['waze']) ?>" target="_blank" rel="noopener">Ir com Waze</a>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ BLOG ============ -->
  <section class="secao" aria-labelledby="titulo-blog">
    <div class="container">
      <div class="linha-titulo">
        <div class="cabecalho-secao">
          <p class="sobretitulo">Blog</p>
          <h2 id="titulo-blog">Informação para <em>cuidar melhor</em></h2>
        </div>
        <a href="/blog/" class="link-seta">Ver todos os artigos <?= icone('seta') ?></a>
      </div>
      <div class="grade grade--3">
        <?php foreach ($posts_recentes as $p) componente('card-post', ['post' => $p]); ?>
      </div>
    </div>
  </section>

  <!-- ============ PERGUNTAS ============ -->
  <section class="secao secao--branca" aria-labelledby="titulo-faq">
    <div class="container faq">
      <div class="cabecalho-secao">
        <p class="sobretitulo">Dúvidas frequentes</p>
        <h2 id="titulo-faq">Perguntas <em>frequentes</em></h2>
        <p>Não encontrou sua resposta? Nosso atendimento responde pelo WhatsApp.</p>
        <a class="btn btn--whatsapp" href="<?= e(whatsapp_url('Olá! Vim pelo site da GastriClin e tenho uma dúvida.')) ?>" target="_blank" rel="noopener" data-cta="whatsapp" data-origem="home-faq">
          <?= icone('whatsapp') ?> Tirar dúvidas
        </a>
      </div>
      <?php componente('perguntas', ['perguntas' => $perguntas]); ?>
    </div>
  </section>
<?php require APP . '/layout/fim.php'; ?>
