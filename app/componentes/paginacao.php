<?php
// Navegação entre páginas (blog). Variáveis: $atual, $total, $url (função: número -> endereço)
if ($total <= 1) return;

// Até 7 páginas mostra todos os números; acima disso: primeira, última, atual ±1 e reticências
$numeros = range(1, $total);
if ($total > 7) {
    $numeros = array_values(array_unique(array_filter(
        [1, $atual - 1, $atual, $atual + 1, $total],
        fn($n) => $n >= 1 && $n <= $total
    )));
    sort($numeros);
}
?>
<nav class="paginacao" aria-label="Páginas do blog">
  <?php if ($atual > 1): ?>
    <a class="paginacao__seta" href="<?= e($url($atual - 1)) ?>" rel="prev"><?= icone('seta', 18, 'paginacao__seta-esq') ?> Mais recentes</a>
  <?php else: ?>
    <span class="paginacao__seta is-desativada" aria-hidden="true"><?= icone('seta', 18, 'paginacao__seta-esq') ?> Mais recentes</span>
  <?php endif; ?>

  <ol class="paginacao__numeros">
    <?php $anterior = 0; foreach ($numeros as $n): ?>
      <?php if ($n - $anterior > 1): ?><li class="paginacao__reticencias" aria-hidden="true">…</li><?php endif; ?>
      <li>
        <?php if ($n === $atual): ?>
          <span class="paginacao__numero is-atual" aria-current="page"><span class="visualmente-oculto">Página </span><?= $n ?></span>
        <?php else: ?>
          <a class="paginacao__numero" href="<?= e($url($n)) ?>"><span class="visualmente-oculto">Página </span><?= $n ?></a>
        <?php endif; ?>
      </li>
    <?php $anterior = $n; endforeach; ?>
  </ol>

  <?php if ($atual < $total): ?>
    <a class="paginacao__seta" href="<?= e($url($atual + 1)) ?>" rel="next">Mais antigos <?= icone('seta', 18) ?></a>
  <?php else: ?>
    <span class="paginacao__seta is-desativada" aria-hidden="true">Mais antigos <?= icone('seta', 18) ?></span>
  <?php endif; ?>
</nav>
