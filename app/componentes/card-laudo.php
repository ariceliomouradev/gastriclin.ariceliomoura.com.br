<?php
// Card horizontal "Baixe seu laudo": instruções + botão que abre o sistema de resultados (resultados.app.br)
// dentro de um modal. Sem JavaScript, o botão é um link normal para o sistema (nova aba).
// O sistema de resultados é externo e não tem integração com o site: o paciente digita código e senha lá;
// este site nunca recebe nem guarda esses dados.
$url = SITE['links']['resultados'];
$passos = [
    ['Clique em “Acessar meus resultados”', 'Aguarde a tela de login abrir.'],
    ['Digite seus dados para entrar', 'Informe seu <strong>usuário</strong> e <strong>senha</strong>. Depois, digite os <strong>4 últimos dígitos do seu celular</strong> cadastrado e confirme.'],
    ['Baixe o que precisar', 'Na lista de exames, clique em <strong>Laudo</strong> e/ou <strong>Imagens</strong>.'],
];
?>
<section class="card-laudo" aria-labelledby="titulo-laudo">
  <div class="card-laudo__intro">
    <span class="card-servico__icone card-laudo__icone"><?= icone('documento', 28) ?></span>
    <p class="card-servico__tipo">Resultados de exames</p>
    <h3 id="titulo-laudo" class="card-laudo__titulo">Baixe seu laudo e imagens do seu exame</h3>
    <p class="card-laudo__resumo">Seu resultado na palma da mão, de forma rápida e prática. Assim que o médico liberar, é só entrar e baixar, quando e onde você quiser.</p>
    <div class="card-laudo__acoes">
      <a class="btn btn--agendar" href="<?= e($url) ?>" target="_blank" rel="noopener" data-laudo-abrir data-origem="home-laudo">
        <?= icone('documento') ?> Acessar meus resultados <span class="visualmente-oculto">(abre o sistema de resultados)</span>
      </a>
      <a class="card-laudo__ajuda" href="<?= e(whatsapp_url('Olá! Vim pelo site da GastriClin e preciso de ajuda para acessar o resultado do meu exame.')) ?>" target="_blank" rel="noopener" data-cta="whatsapp" data-origem="home-laudo-ajuda">
        Perdeu seu usuário ou senha? Fale com a gente <?= icone('seta', 16) ?><span class="visualmente-oculto"> (WhatsApp, nova aba)</span>
      </a>
    </div>
  </div>

  <ol class="card-laudo__passos">
    <?php foreach ($passos as $i => [$titulo, $texto]): ?>
      <li class="card-laudo__passo">
        <span class="card-laudo__num" aria-hidden="true"><?= $i + 1 ?></span>
        <div>
          <p class="card-laudo__passo-titulo"><?= $titulo ?></p>
          <p><?= $texto ?></p>
        </div>
      </li>
    <?php endforeach; ?>
  </ol>
</section>

<!-- Modal: o sistema de resultados abre aqui dentro e só é carregado quando o paciente clica no botão -->
<dialog class="laudo-modal" data-laudo-modal aria-labelledby="laudo-modal-titulo" data-url="<?= e($url) ?>">
  <div class="laudo-modal__painel">
    <div class="laudo-modal__topo">
      <p class="laudo-modal__titulo" id="laudo-modal-titulo"><?= icone('documento', 20) ?> Meus resultados</p>
      <a class="laudo-modal__externo" href="<?= e($url) ?>" target="_blank" rel="noopener" data-laudo-externo>
        <?= icone('seta-externa', 18) ?> <span>Abrir em outra aba</span>
      </a>
      <button type="button" class="laudo-modal__fechar" data-laudo-fechar>
        <?= icone('fechar', 20) ?> <span>Fechar</span>
      </button>
    </div>
    <div class="laudo-modal__corpo">
      <div class="laudo-modal__carregando" data-laudo-carregando role="status">
        <span class="agenda__spinner" aria-hidden="true"></span>
        <span>Carregando o sistema de resultados…</span>
      </div>
      <iframe class="laudo-modal__quadro" data-laudo-quadro title="Sistema de resultados de exames da GastriClin" referrerpolicy="strict-origin" allow="fullscreen" src="about:blank"></iframe>
    </div>
    <p class="laudo-modal__rodape">Não abriu ou o login não funcionou neste aparelho? Use <strong>Abrir em outra aba</strong>, no topo.</p>
  </div>
</dialog>
