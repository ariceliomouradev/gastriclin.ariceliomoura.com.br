<?php
// Página de consulta ou exame (/gastroenterologia/, /endoscopia/ ...). Recebe $servico do roteador.
$s = $servico;
$medicos = medicos_do_servico($s['slug']);
$outros = array_values(array_filter(servicos(), fn($x) => $x['tipo'] === $s['tipo'] && $x['slug'] !== $s['slug']));
$grupo = $s['tipo'] === 'consulta' ? ['rotulo' => 'Consultas', 'href' => '/especialidades/'] : ['rotulo' => 'Exames', 'href' => '/exames/'];
$artigos = posts_por_temas($s['temas']);
$end = SITE['endereco'];

$pagina = [
    'titulo' => $s['seo']['title'],
    'descricao' => $s['seo']['description'],
    'servico' => $s['nome_agendamento'],
    'json_ld' => [[
        '@context' => 'https://schema.org',
        '@type' => 'MedicalWebPage',
        'name' => $s['seo']['title'],
        'description' => $s['seo']['description'],
        'url' => SITE['url'] . "/{$s['slug']}/",
        'about' => $s['tipo'] === 'exame'
            ? ['@type' => 'MedicalProcedure', 'name' => $s['nome']]
            : ['@type' => 'MedicalSpecialty', 'name' => $s['nome']],
        'provider' => ['@id' => SITE['url'] . '/#clinica'],
    ]],
];
require APP . '/layout/inicio.php';
?>
  <section class="topo-servico fundo-escuro">
    <div class="container topo-servico__grade">
      <div>
        <?php componente('trilha', ['itens' => [$grupo, ['rotulo' => $s['nome'], 'href' => "/{$s['slug']}/"]]]); ?>
        <p class="sobretitulo"><?= $s['tipo'] === 'consulta' ? 'Consulta especializada' : 'Exame e procedimento' ?></p>
        <h1><?= e($s['nome']) ?> <em>em Fortaleza</em></h1>
        <p class="topo-servico__lead"><?= e($s['intro'][0]) ?></p>
        <?php componente('botoes-agendar', ['servico' => $s['nome_agendamento'], 'origem' => "pagina-{$s['slug']}-topo"]); ?>
        <ul class="topo-servico__destaques">
          <?php foreach ($s['destaques'] as $d): ?><li><?= icone('check', 18) ?> <?= e($d) ?></li><?php endforeach; ?>
        </ul>
      </div>
      <div class="topo-servico__img">
        <?= imagem($s['imagem'], '', ['sizes' => '(max-width: 900px) 70vw, 420px', 'carregar' => 'eager']) ?>
      </div>
    </div>
  </section>

  <section class="secao secao--branca">
    <div class="container conteudo-servico">
      <div class="prosa">
        <?php foreach (array_slice($s['intro'], 1) as $p): ?><p><?= e($p) ?></p><?php endforeach; ?>
        <h2><?= e($s['lista_titulo']) ?></h2>
        <ul class="lista-indicacoes">
          <?php foreach ($s['lista'] as $i): ?>
            <li>
              <?= icone('check', 20) ?>
              <span><strong><?= e($i['titulo']) ?></strong><?= !empty($i['texto']) ? ' – ' . e($i['texto']) : '' ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php if (!empty($s['como_funciona'])): ?>
          <h2>Como funciona</h2>
          <?php foreach ($s['como_funciona'] as $p): ?><p><?= e($p) ?></p><?php endforeach; ?>
        <?php endif; ?>
        <p class="aviso">
          Estas informações são educativas e não substituem a avaliação médica. A indicação de consultas e exames é feita pelo médico.
        </p>
      </div>

      <aside class="cartao-agendar" aria-label="Agendar <?= e($s['nome_agendamento']) ?>">
        <p class="sobretitulo">Agende agora</p>
        <p class="cartao-agendar__titulo"><?= e($s['nome_agendamento']) ?></p>
        <p>Escolha o médico e o horário na agenda online ou fale com nosso atendimento.</p>
        <?php componente('botoes-agendar', ['servico' => $s['nome_agendamento'], 'origem' => "pagina-{$s['slug']}-lateral", 'tamanho' => 'pequeno']); ?>
        <hr>
        <p class="cartao-agendar__local">
          <?= icone('local', 20) ?>
          <span><?= e("{$end['rua']} – {$end['bairro']}, {$end['cidade']}") ?></span>
        </p>
        <a href="/convenios/" class="link-seta">Ver convênios aceitos <?= icone('seta', 18) ?></a>
      </aside>
    </div>
  </section>

  <?php if ($medicos): ?>
    <section class="secao" aria-labelledby="titulo-medicos">
      <div class="container">
        <div class="cabecalho-secao">
          <p class="sobretitulo">Quem atende</p>
          <h2 id="titulo-medicos">Médicos que realizam <em><?= $s['tipo'] === 'consulta' ? 'esta consulta' : 'este exame' ?></em></h2>
        </div>
        <div class="grade grade--medicos">
          <?php foreach ($medicos as $m) componente('card-medico', ['pessoa' => $m]); ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section class="secao secao--branca" aria-labelledby="titulo-convenios">
    <div class="container">
      <div class="linha-titulo">
        <div class="cabecalho-secao">
          <p class="sobretitulo">Convênios</p>
          <h2 id="titulo-convenios">Atendemos o <em>seu plano</em></h2>
          <p>Confirme a cobertura de <?= $s['tipo'] === 'consulta' ? 'consultas' : 'exames' ?> do seu plano pelo WhatsApp.</p>
        </div>
        <a href="/convenios/" class="link-seta">Ver todos <?= icone('seta') ?></a>
      </div>
      <?php componente('grade-convenios', ['compacto' => true]); ?>
    </div>
  </section>

  <?php if ($artigos): ?>
    <section class="secao" aria-labelledby="titulo-artigos">
      <div class="container">
        <div class="linha-titulo">
          <div class="cabecalho-secao">
            <p class="sobretitulo">Blog</p>
            <h2 id="titulo-artigos">Leia <em>também</em></h2>
          </div>
          <a href="/blog/" class="link-seta">Todos os artigos <?= icone('seta') ?></a>
        </div>
        <div class="grade grade--3">
          <?php foreach ($artigos as $p) componente('card-post', ['post' => $p]); ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section class="secao secao--branca" aria-labelledby="titulo-outros">
    <div class="container">
      <div class="cabecalho-secao">
        <p class="sobretitulo"><?= $s['tipo'] === 'consulta' ? 'Outras especialidades' : 'Outros exames' ?></p>
        <h2 id="titulo-outros">Você também pode <em>agendar</em></h2>
      </div>
      <div class="grade <?= count($outros) >= 3 ? 'grade--3' : 'grade--2' ?>">
        <?php foreach ($outros as $o) componente('card-servico', ['servico' => $o, 'origem' => "pagina-{$s['slug']}-outros"]); ?>
      </div>
    </div>
  </section>
<?php require APP . '/layout/fim.php'; ?>
