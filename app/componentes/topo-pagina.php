<?php
// Topo roxo das páginas internas.
// Variáveis: $trilha, $sobretitulo, $titulo_html (pode conter <em>), $lead, $extra_html (ex.: botões)
$lead ??= '';
$extra_html ??= '';
?>
<section class="topo-pagina fundo-escuro">
  <div class="container">
    <?php componente('trilha', ['itens' => $trilha]); ?>
    <p class="sobretitulo"><?= e($sobretitulo) ?></p>
    <h1><?= $titulo_html ?></h1>
    <?php if ($lead): ?><p class="topo-pagina__lead"><?= e($lead) ?></p><?php endif; ?>
    <?= $extra_html ?>
  </div>
</section>
