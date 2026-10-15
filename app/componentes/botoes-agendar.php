<?php
// Os dois caminhos de agendamento do site:
// 1) Doctoralia: o paciente escolhe especialidade, médico e horário.
// 2) WhatsApp: abre a conversa com a mensagem já preenchida e o atendimento segue pelo agente da clínica.
// Variáveis: $origem (obrigatória, para o Analytics), $servico, $tamanho ('normal'|'pequeno'),
//            $texto_agendar, $texto_whatsapp, $classe
$servico ??= null;
$tamanho ??= 'normal';
$texto_agendar ??= 'Agendar online';
$texto_whatsapp ??= 'Agendar pelo WhatsApp';
$classe ??= '';
$extra = $tamanho === 'pequeno' ? ' btn--pequeno' : '';
$dados = 'data-origem="' . e($origem) . '"' . ($servico ? ' data-servico="' . e($servico) . '"' : '');
?>
<div class="botoes-agendar<?= $classe ? ' ' . e($classe) : '' ?>">
  <a class="btn btn--agendar<?= $extra ?>" href="/agendar/" data-cta="doctoralia" <?= $dados ?>>
    <?= icone('calendario') ?>
    <?= e($texto_agendar) ?>
  </a>
  <a class="btn btn--whatsapp<?= $extra ?>" href="<?= e(whatsapp_url(mensagem_agendamento($servico))) ?>" target="_blank" rel="noopener" data-cta="whatsapp" <?= $dados ?>>
    <?= icone('whatsapp') ?>
    <?= e($texto_whatsapp) ?>
    <span class="visualmente-oculto"> (abre o WhatsApp em nova aba)</span>
  </a>
</div>
