<?php
$titulo_tela = 'Primeiro acesso';
require __DIR__ . '/_topo.php';
?>
<section class="entrar entrar--largo">
  <img src="/assets/img/marca/gastriclin-roxo-340.webp" alt="GastriClin" width="240" height="68">
  <h1>Primeiro acesso ao painel</h1>
  <p>Crie o usuário administrador. Esta tela só aparece enquanto não existe nenhum usuário.</p>
  <ol class="passos-instalar">
    <li>No <strong>hPanel da Hostinger</strong>, abra o <strong>Gerenciador de Arquivos</strong>.</li>
    <li>Entre na pasta do site (<code>public_html</code>) e depois em <code>conteudo</code>.</li>
    <li>Abra o arquivo <code>chave-instalacao.txt</code> e copie o código (formato XXXX-XXXX-XXXX).</li>
  </ol>
  <?php if ($erro): ?><p class="aviso-painel aviso-painel--erro" role="alert"><?= e($erro) ?></p><?php endif; ?>
  <form method="post" action="/admin/?acao=instalar" class="formulario">
    <?= campo_csrf() ?>
    <label>Código de instalação
      <input type="text" name="chave" required autocomplete="off" placeholder="XXXX-XXXX-XXXX">
    </label>
    <label>Seu nome
      <input type="text" name="nome" required value="<?= e($_POST['nome'] ?? '') ?>">
    </label>
    <label>E-mail (será o login)
      <input type="email" name="email" required autocomplete="username" value="<?= e($_POST['email'] ?? '') ?>">
    </label>
    <label>Senha <small>(mínimo 10 caracteres)</small>
      <input type="password" name="senha" required minlength="10" autocomplete="new-password">
    </label>
    <label>Repita a senha
      <input type="password" name="senha2" required minlength="10" autocomplete="new-password">
    </label>
    <button type="submit" class="botao botao--primario">Criar administrador e entrar</button>
  </form>
</section>
<?php require __DIR__ . '/_rodape.php'; ?>
