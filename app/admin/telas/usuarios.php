<?php
$titulo_tela = 'Usuários';
require __DIR__ . '/_topo.php';
?>
<div class="cabecalho-tela">
  <div>
    <h1>Usuários do painel</h1>
    <p>A equipe de TI cria, edita e publica os artigos entregues pelo marketing. Administradores também gerenciam os usuários.</p>
  </div>
</div>

<table class="tabela">
  <thead><tr><th>Nome</th><th>E-mail</th><th>Papel</th><th>Acesso</th><th><span class="visualmente-oculto">Ações</span></th></tr></thead>
  <tbody>
    <?php foreach (usuarios() as $u): ?>
      <tr>
        <td><?= e($u['nome']) ?></td>
        <td><?= e($u['email']) ?></td>
        <td><?= e(PAPEIS[$u['papel']] ?? $u['papel']) ?></td>
        <td><?= ($u['ativo'] ?? true) ? 'Ativo' : '<span class="pendente-painel">Bloqueado</span>' ?></td>
        <td class="tabela__acoes">
          <?php if ($u['email'] !== $usuario['email']): ?>
            <form method="post" action="/admin/?acao=usuarios">
              <?= campo_csrf() ?>
              <input type="hidden" name="operacao" value="alternar">
              <input type="hidden" name="email" value="<?= e($u['email']) ?>">
              <button type="submit" class="link-botao"><?= ($u['ativo'] ?? true) ? 'Bloquear' : 'Reativar' ?></button>
            </form>
          <?php else: ?>
            <small>(você)</small>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h2 class="subtitulo-painel">Novo usuário</h2>
<form method="post" action="/admin/?acao=usuarios" class="formulario cartao-painel formulario--grade">
  <?= campo_csrf() ?>
  <input type="hidden" name="operacao" value="criar">
  <label class="campo">Nome <input type="text" name="nome" required></label>
  <label class="campo">E-mail <input type="email" name="email" required></label>
  <label class="campo">Papel
    <select name="papel">
      <?php foreach (PAPEIS as $valor => $rotulo): ?><option value="<?= $valor ?>"><?= e($rotulo) ?></option><?php endforeach; ?>
    </select>
  </label>
  <label class="campo">Senha inicial <small>(mín. 10 caracteres)</small> <input type="password" name="senha" required minlength="10" autocomplete="new-password"></label>
  <button type="submit" class="botao botao--primario">Criar usuário</button>
</form>
<?php require __DIR__ . '/_rodape.php'; ?>
