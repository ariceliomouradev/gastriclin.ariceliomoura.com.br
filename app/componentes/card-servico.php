<?php
// Card de consulta ou exame: o card inteiro leva à página do serviço;
// os botões levam direto ao agendamento (1 clique a partir da página inicial).
// Variáveis: $servico (array), $origem, $titulo ('h3' | 'h2')
$s = $servico;
$titulo ??= 'h3';
$dados = 'data-origem="' . e($origem) . '" data-servico="' . e($s['nome_agendamento']) . '"';
?>
<article class="card-servico">
  <span class="card-servico__icone"><?= icone($s['icone'], 28) ?></span>
  <p class="card-servico__tipo"><?= $s['tipo'] === 'consulta' ? 'Consulta' : 'Exame' ?></p>
  <<?= $titulo ?> class="card-servico__titulo">
    <a href="/<?= $s['slug'] ?>/"><?= e($s['nome']) ?></a>
  </<?= $titulo ?>>
  <p class="card-servico__resumo"><?= e($s['resumo']) ?></p>
  <div class="card-servico__rodape">
    <span class="card-servico__mais" aria-hidden="true">Saiba mais <?= icone('seta', 18) ?></span>
    <div class="card-servico__acoes">
      <a class="btn btn--agendar btn--pequeno" href="/agendar/" data-cta="doctoralia" <?= $dados ?>>
        Agendar<span class="visualmente-oculto"> <?= e($s['nome_agendamento']) ?> online</span>
      </a>
      <a class="btn btn--whatsapp btn--pequeno card-servico__whats" href="<?= e(whatsapp_url(mensagem_agendamento($s['nome_agendamento']))) ?>" target="_blank" rel="noopener" data-cta="whatsapp" <?= $dados ?>>
        <?= icone('whatsapp') ?>
        <span class="visualmente-oculto">Agendar <?= e($s['nome_agendamento']) ?> pelo WhatsApp (nova aba)</span>
      </a>
    </div>
  </div>
</article>
