<?php
// /convenios/ – planos aceitos.
$lista = convenios();
$pagina = [
    'titulo' => 'Convênios Atendidos | GastriClin Fortaleza',
    'descricao' => 'A GastriClin atende ' . count($lista) . ' convênios em Fortaleza, como Amil, Bradesco Saúde, SulAmérica, Cassi, GEAP, Issec e Saúde Caixa. Consultas e exames.',
];
require APP . '/layout/inicio.php';

componente('topo-pagina', [
    'trilha' => [['rotulo' => 'Convênios', 'href' => '/convenios/']],
    'sobretitulo' => 'Convênios',
    'titulo_html' => 'Atendemos o <em>seu convênio</em>',
    'lead' => 'Consultas e exames pelos principais planos de saúde de Fortaleza.',
]);
?>
  <section class="secao">
    <div class="container">
      <?php componente('grade-convenios'); ?>
      <p class="visualmente-oculto">Lista: <?= e(implode(', ', array_column($lista, 'nome'))) ?>.</p>

      <div class="duvida">
        <?= icone('cartao', 32) ?>
        <div>
          <h2>Não encontrou o seu plano?</h2>
          <p>Fale com a gente pelo WhatsApp para confirmar a cobertura do seu plano para consultas e exames.</p>
        </div>
        <a class="btn btn--whatsapp" href="<?= e(whatsapp_url('Olá! Vim pelo site da GastriClin e gostaria de confirmar se vocês atendem o meu convênio.')) ?>" target="_blank" rel="noopener" data-cta="whatsapp" data-origem="convenios-duvida">
          <?= icone('whatsapp') ?> Consultar meu convênio
        </a>
      </div>
    </div>
  </section>

  <section class="secao secao--branca">
    <div class="container centro">
      <h2>Pronto para <em>agendar</em>?</h2>
      <?php componente('botoes-agendar', ['origem' => 'convenios-fim', 'classe' => 'centro__botoes']); ?>
    </div>
  </section>
<?php require APP . '/layout/fim.php'; ?>
