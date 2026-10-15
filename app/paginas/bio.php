<?php
// /bio/ – página de links usada na bio do Instagram (substitui /bio/, /links/ e /links-2/ do site antigo).
$end = SITE['endereco'];
$links = [
    ['href' => '/agendar/', 'rotulo' => 'Agendar consulta ou exame', 'detalhe' => 'Agenda online', 'icone' => 'calendario', 'cta' => 'doctoralia', 'estilo' => 'destaque'],
    ['href' => whatsapp_url('Olá! Vim pelo Instagram da GastriClin e gostaria de agendar um atendimento.'), 'rotulo' => 'Agendar pelo WhatsApp', 'detalhe' => 'Fale com nosso atendimento', 'icone' => 'whatsapp', 'cta' => 'whatsapp', 'estilo' => 'whats'],
    ['href' => '/#servicos', 'rotulo' => 'Consultas e exames', 'detalhe' => 'Veja tudo o que oferecemos', 'icone' => 'estetoscopio'],
    ['href' => '/convenios/', 'rotulo' => 'Convênios', 'detalhe' => 'Planos aceitos', 'icone' => 'cartao'],
    ['href' => SITE['links']['google_maps'], 'rotulo' => 'Como chegar', 'detalhe' => "{$end['rua']} – {$end['bairro']}", 'icone' => 'local'],
    ['href' => '/blog/', 'rotulo' => 'Blog', 'detalhe' => 'Dicas de saúde digestiva', 'icone' => 'seta'],
];
$pagina = [
    'titulo' => 'Links | GastriClin',
    'descricao' => 'Agende sua consulta ou exame na GastriClin, em Fortaleza.',
    'noindex' => true,
];
require APP . '/layout/inicio.php';
?>
  <section class="bio fundo-escuro">
    <div class="bio__caixa">
      <h1 class="visualmente-oculto">GastriClin – links de agendamento</h1>
      <?= imagem('marca/gastriclin-branco', 'GastriClin – Centro de Medicina Digestiva', ['largura' => 240, 'carregar' => 'eager']) ?>
      <ul>
        <?php foreach ($links as $l):
            $externo = str_starts_with($l['href'], 'http');
            $classe = 'bio__link' . (isset($l['estilo']) ? ' bio__link--' . $l['estilo'] : ''); ?>
          <li>
            <a href="<?= e($l['href']) ?>" class="<?= $classe ?>"<?= $externo ? ' target="_blank" rel="noopener"' : '' ?><?= isset($l['cta']) ? ' data-cta="' . $l['cta'] . '" data-origem="bio-instagram"' : '' ?>>
              <?= icone($l['icone']) ?>
              <span>
                <strong><?= e($l['rotulo']) ?></strong>
                <small><?= e($l['detalhe']) ?></small>
              </span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
<?php require APP . '/layout/fim.php'; ?>
