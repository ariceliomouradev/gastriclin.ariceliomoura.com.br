<?php
$titulo_tela = 'Entrar';
require __DIR__ . '/_topo.php';
?>
<section class="entrar">
  <img src="/assets/img/marca/gastriclin-roxo-340.webp" alt="GastriClin" width="240" height="68">
  <h1>Painel do site</h1>
  <p>Acesso restrito à equipe da GastriClin.</p>
  <?php if ($erro): ?><p class="aviso-painel aviso-painel--erro" role="alert"><?= e($erro) ?></p><?php endif; ?>
  <form method="post" action="/admin/?acao=entrar" class="formulario">
    <?= campo_csrf() ?>
    <label>E-mail
      <input type="email" name="email" required autocomplete="username" value="<?= e($_POST['email'] ?? '') ?>">
    </label>
    <label>Senha
      <input type="password" name="senha" required autocomplete="current-password">
    </label>
    <button type="submit" class="botao botao--primario">Entrar</button>
  </form>
</section>
<?php require __DIR__ . '/_rodape.php'; ?>
