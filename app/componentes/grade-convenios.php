<?php
// Logos dos convênios. Variável: $compacto (bool)
$compacto ??= false;
?>
<ul class="convenios<?= $compacto ? ' convenios--compacto' : '' ?>" aria-label="Convênios aceitos">
  <?php foreach (convenios() as $c): ?>
    <li class="convenio"><?= imagem($c['logo'], $c['nome'], ['altura' => $compacto ? 56 : 72]) ?></li>
  <?php endforeach; ?>
</ul>
