<?php
// Card de profissional. Variáveis: $pessoa (array), $titulo ('h3' | 'h2')
$p = $pessoa;
$titulo ??= 'h3';
?>
<article class="card-medico">
  <div class="card-medico__foto">
    <?= imagem($p['foto'], 'Foto de ' . nome_exibicao($p), ['sizes' => '(max-width: 640px) 80vw, 280px']) ?>
  </div>
  <div class="card-medico__info">
    <<?= $titulo ?> class="card-medico__nome"><a href="/<?= $p['slug'] ?>/"><?= e($p['nome_curto']) ?></a></<?= $titulo ?>>
    <p class="card-medico__cargo"><?= e($p['cargo']) ?></p>
    <?php if (!empty($p['registro'])): ?><p class="card-medico__registro"><?= e($p['registro']) ?></p><?php endif; ?>
    <span class="card-medico__mais" aria-hidden="true">Ver perfil <?= icone('seta', 16) ?></span>
  </div>
</article>
