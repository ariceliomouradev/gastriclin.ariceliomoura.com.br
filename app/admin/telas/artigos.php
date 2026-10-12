<?php
$titulo_tela = 'Artigos';
require __DIR__ . '/_topo.php';
?>
<div class="cabecalho-tela">
  <div>
    <h1>Artigos do blog</h1>
    <p><?= count(posts(false)) ?> artigos · <?= $contagem['publicado'] ?? 0 ?> publicados · <?= $contagem['rascunho'] ?? 0 ?> rascunhos</p>
  </div>
  <a href="/admin/?acao=editar" class="botao botao--primario">+ Novo artigo</a>
</div>

<form method="get" action="/admin/" class="filtros">
  <input type="hidden" name="acao" value="artigos">
  <input type="search" name="busca" value="<?= e($busca) ?>" placeholder="Buscar pelo título" aria-label="Buscar pelo título">
  <select name="status" aria-label="Filtrar por situação">
    <option value="">Todas as situações</option>
    <?php foreach (STATUS_POST as $valor => $rotulo): ?>
      <option value="<?= $valor ?>"<?= $filtro === $valor ? ' selected' : '' ?>><?= e($rotulo) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="botao">Filtrar</button>
</form>

<?php if (!$lista): ?>
  <p class="vazio">Nenhum artigo encontrado.</p>
<?php else: ?>
  <table class="tabela">
    <thead>
      <tr><th>Título</th><th>Situação</th><th>Publicação</th><th>Última edição</th><th><span class="visualmente-oculto">Ações</span></th></tr>
    </thead>
    <tbody>
      <?php foreach ($lista as $p): ?>
        <tr>
          <td>
            <a href="/admin/?acao=editar&amp;slug=<?= e($p['slug']) ?>" class="tabela__titulo"><?= e($p['titulo']) ?></a>
            <small>/<?= e($p['slug']) ?>/</small>
          </td>
          <td><span class="selo selo--<?= e($p['status']) ?>"><?= e(STATUS_POST[$p['status']] ?? $p['status']) ?></span></td>
          <td><?= e(date('d/m/Y', strtotime($p['publicado_em']))) ?></td>
          <td><?= e(date('d/m/Y', strtotime($p['atualizado_em'] ?: $p['publicado_em']))) ?><?= !empty($p['editado_por']) ? '<small>' . e($p['editado_por']) . '</small>' : '' ?></td>
          <td class="tabela__acoes">
            <a href="/admin/?acao=editar&amp;slug=<?= e($p['slug']) ?>">Editar</a>
            <?php if ($p['status'] === 'publicado'): ?>
              <a href="/<?= e($p['slug']) ?>/" target="_blank" rel="noopener">Ver ↗</a>
            <?php else: ?>
              <a href="/admin/?acao=previa&amp;slug=<?= e($p['slug']) ?>" target="_blank" rel="noopener">Prévia ↗</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
<?php require __DIR__ . '/_rodape.php'; ?>
