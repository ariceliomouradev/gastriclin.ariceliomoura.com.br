<?php
// "Agende em poucos cliques": os três passos do agendamento.
$passos = [
    ['icone' => 'estetoscopio', 'titulo' => 'Escolha o que precisa', 'texto' => 'Consulta com especialista ou exame. Use o menu ou os cards desta página.'],
    ['icone' => 'calendario', 'titulo' => 'Clique em “Agendar”', 'texto' => 'Na agenda online, selecione a especialidade, o médico e o melhor horário.'],
    ['icone' => 'whatsapp', 'titulo' => 'Prefere conversar?', 'texto' => 'Toque em “WhatsApp” e nosso atendimento agenda para você e tira suas dúvidas.'],
];
?>
<ol class="passos">
  <?php foreach ($passos as $i => $p): ?>
    <li class="passo">
      <span class="passo__num" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span>
      <span class="passo__icone"><?= icone($p['icone'], 26) ?></span>
      <h3><?= e($p['titulo']) ?></h3>
      <p><?= e($p['texto']) ?></p>
    </li>
  <?php endforeach; ?>
</ol>
