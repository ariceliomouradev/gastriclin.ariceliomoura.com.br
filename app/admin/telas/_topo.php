<?php
// Topo comum das telas do painel. Variáveis: $titulo_tela, $usuario (opcional)
$usuario ??= null;
$acao_atual = $_GET['acao'] ?? 'artigos';
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($titulo_tela) ?> · Painel GastriClin</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="stylesheet" href="/assets/css/fontes.css">
    <link rel="stylesheet" href="/admin/admin.css">
    <script src="/admin/admin.js" defer></script>
  </head>
  <body class="painel">
    <?php if ($usuario): ?>
      <header class="painel__topo">
        <a href="/admin/" class="painel__marca">
          <img src="/assets/img/marca/gastriclin-roxo-340.webp" alt="GastriClin" width="161" height="46">
          <span>Painel</span>
        </a>
        <nav class="painel__nav" aria-label="Painel">
          <a href="/admin/"<?= in_array($acao_atual, ['artigos', 'editar'], true) ? ' aria-current="page"' : '' ?>>Artigos</a>
          <a href="/admin/?acao=editar" class="botao botao--primario botao--pequeno">+ Novo artigo</a>
          <?php if (pode('usuarios', $usuario)): ?><a href="/admin/?acao=usuarios"<?= $acao_atual === 'usuarios' ? ' aria-current="page"' : '' ?>>Usuários</a><?php endif; ?>
          <a href="/admin/?acao=senha"<?= $acao_atual === 'senha' ? ' aria-current="page"' : '' ?>>Minha senha</a>
          <a href="/" target="_blank" rel="noopener">Ver site ↗</a>
          <form method="post" action="/admin/?acao=sair" class="painel__sair">
            <?= campo_csrf() ?>
            <button type="submit" class="link-botao"><?= e($usuario['nome']) ?> · Sair</button>
          </form>
        </nav>
      </header>
    <?php endif; ?>
    <main class="painel__conteudo">
      <?php foreach (pegar_avisos() as [$tipo, $mensagem]): ?>
        <p class="aviso-painel aviso-painel--<?= e($tipo) ?>" role="<?= $tipo === 'erro' ? 'alert' : 'status' ?>"><?= e($mensagem) ?></p>
      <?php endforeach; ?>
