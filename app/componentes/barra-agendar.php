<?php
// Barra fixa no rodapé da tela em celulares: os dois botões de agendamento sempre a um toque.
// Variável: $servico (opcional)
$servico ??= null;
$dados = 'data-origem="barra-celular"' . ($servico ? ' data-servico="' . e($servico) . '"' : '');
?>
<div class="barra-agendar" aria-label="Agendamento rápido" role="region">
  <a href="/agendar/" class="barra__btn barra__btn--agendar" data-cta="doctoralia" <?= $dados ?>>
    <?= icone('calendario', 20) ?>
    Agendar online
  </a>
  <a href="<?= e(whatsapp_url(mensagem_agendamento($servico))) ?>" target="_blank" rel="noopener" class="barra__btn barra__btn--whats" data-cta="whatsapp" <?= $dados ?>>
    <?= icone('whatsapp', 20) ?>
    WhatsApp
  </a>
</div>
