<?php
$titulo_tela = 'Minha senha';
require __DIR__ . '/_topo.php';
?>
<div class="cabecalho-tela">
  <div>
    <h1>Minha senha</h1>
    <p><?= e($usuario['nome']) ?> · <?= e($usuario['email']) ?> · <?= e(PAPEIS[$usuario['papel']] ?? $usuario['papel']) ?></p>
  </div>
</div>
<form method="post" action="/admin/?acao=senha" class="formulario cartao-painel formulario--estreito">
  <?= campo_csrf() ?>
  <label class="campo">Senha atual
    <input type="password" name="senha_atual" required autocomplete="current-password">
  </label>
  <label class="campo">Nova senha <small>(mínimo 10 caracteres)</small>
    <input type="password" name="senha_nova" required minlength="10" autocomplete="new-password">
  </label>
  <button type="submit" class="botao botao--primario">Alterar senha</button>
</form>
<?php require __DIR__ . '/_rodape.php'; ?>
