<?php
// Editor de artigo. Variáveis: $post, $usuario
$novo = $post['slug'] === '';
// Se o último envio deu erro, recupera o que foi digitado
$form = $_SESSION['formulario'] ?? null;
unset($_SESSION['formulario']);
$v = fn(string $campo, $padrao) => $form[$campo] ?? $padrao;
$rev = $post['revisao_medica'] ?? [];
$publicado = $post['status'] === 'publicado';
$titulo_tela = $novo ? 'Novo artigo' : 'Editar: ' . $post['titulo'];
require __DIR__ . '/_topo.php';
?>
<div class="cabecalho-tela">
  <div>
    <p class="voltar"><a href="/admin/">← Todos os artigos</a></p>
    <h1><?= $novo ? 'Novo artigo' : 'Editar artigo' ?></h1>
    <?php if (!$novo): ?>
      <p>
        <span class="selo selo--<?= e($post['status']) ?>"><?= e(STATUS_POST[$post['status']]) ?></span>
        <?php if (!empty($post['editado_por'])): ?> · Última edição por <?= e($post['editado_por']) ?> em <?= e(date('d/m/Y', strtotime($post['atualizado_em']))) ?><?php endif; ?>
      </p>
    <?php endif; ?>
  </div>
  <?php if (!$novo): ?>
    <a class="botao" href="<?= $publicado ? '/' . e($post['slug']) . '/' : '/admin/?acao=previa&amp;slug=' . e($post['slug']) ?>" target="_blank" rel="noopener"><?= $publicado ? 'Ver no site ↗' : 'Pré-visualizar ↗' ?></a>
  <?php endif; ?>
</div>

<form method="post" action="/admin/?acao=salvar" enctype="multipart/form-data" class="editor" data-editor-form>
  <?= campo_csrf() ?>
  <input type="hidden" name="slug_original" value="<?= e($post['slug']) ?>">

  <div class="editor__principal">
    <label class="campo">Título do artigo
      <input type="text" name="titulo" required maxlength="140" value="<?= e($v('titulo', $post['titulo'])) ?>" data-titulo>
    </label>

    <label class="campo">Endereço da página
      <span class="campo__prefixo">gastriclin.com.br/<input type="text" name="slug" value="<?= e($v('slug', $post['slug'])) ?>" pattern="[a-z0-9]+(-[a-z0-9]+)*" data-slug<?= $publicado ? ' readonly' : '' ?>>/</span>
      <small><?= $publicado ? 'O endereço de um artigo publicado não muda, para não quebrar links no Google.' : 'Gerado a partir do título. Só letras minúsculas, números e hífens.' ?></small>
    </label>

    <div class="campo">
      <span id="rotulo-conteudo">Texto do artigo</span>
      <div class="barra-editor" role="toolbar" aria-label="Formatação" data-barra>
        <button type="button" data-cmd="formatBlock" data-valor="h2" title="Título de seção">Título</button>
        <button type="button" data-cmd="formatBlock" data-valor="h3" title="Subtítulo">Subtítulo</button>
        <button type="button" data-cmd="formatBlock" data-valor="p" title="Parágrafo normal">Parágrafo</button>
        <span class="barra-editor__sep"></span>
        <button type="button" data-cmd="bold" title="Negrito"><strong>N</strong></button>
        <button type="button" data-cmd="italic" title="Itálico"><em>I</em></button>
        <span class="barra-editor__sep"></span>
        <button type="button" data-cmd="insertUnorderedList" title="Lista com marcadores">• Lista</button>
        <button type="button" data-cmd="insertOrderedList" title="Lista numerada">1. Lista</button>
        <button type="button" data-cmd="formatBlock" data-valor="blockquote" title="Citação">“ Citação</button>
        <span class="barra-editor__sep"></span>
        <button type="button" data-cmd="createLink" title="Inserir link">Link</button>
        <button type="button" data-cmd="unlink" title="Remover link">Tirar link</button>
        <button type="button" data-cmd="removeFormat" title="Limpar formatação">Limpar</button>
      </div>
      <div class="area-editor prosa" contenteditable="true" role="textbox" aria-multiline="true" aria-labelledby="rotulo-conteudo" data-area><?= html_seguro((string) $v('conteudo', $post['conteudo_html'])) ?></div>
      <textarea name="conteudo" hidden data-conteudo></textarea>
      <small>Dica: ao colar texto de outro lugar, a formatação é limpa automaticamente.</small>
    </div>
  </div>

  <aside class="editor__lateral">
    <fieldset class="cartao-painel">
      <legend>Publicação</legend>
      <label class="campo">Data de publicação
        <input type="date" name="publicado_em" value="<?= e($v('publicado_em', $post['publicado_em'])) ?>">
      </label>
      <div class="acoes-publicar">
        <button type="submit" name="botao" value="publicar" class="botao botao--primario"><?= $publicado ? 'Atualizar no site' : 'Publicar no site' ?></button>
        <button type="submit" name="botao" value="rascunho" class="botao"><?= $publicado ? 'Tirar do site (voltar a rascunho)' : 'Salvar rascunho' ?></button>
      </div>
    </fieldset>

    <fieldset class="cartao-painel">
      <legend>Revisão médica <small>(opcional)</small></legend>
      <p class="dica-painel">Se um médico da clínica revisou o texto, informe aqui: o nome aparece no artigo e passa mais confiança ao leitor e ao Google.</p>
      <label class="campo campo--linha"><input type="checkbox" name="revisado" value="1"<?= $v('revisado', !empty($rev['revisado'])) ? ' checked' : '' ?>> Conteúdo revisado por médico(a)</label>
      <label class="campo">Nome e CRM do(a) revisor(a)
        <input type="text" name="revisor" placeholder="Ex.: Dr. Lucas Mapurunga – CRM-CE 21069" value="<?= e($v('revisor', $rev['por'] ?? '')) ?>">
      </label>
      <label class="campo">Data da revisão
        <input type="date" name="data_revisao" value="<?= e($v('data_revisao', $rev['data'] ?? '')) ?>">
      </label>
    </fieldset>

    <fieldset class="cartao-painel">
      <legend>Google</legend>
      <label class="campo">Título para o Google <small>(opcional)</small>
        <input type="text" name="titulo_seo" maxlength="70" value="<?= e($v('titulo_seo', $post['titulo_seo'])) ?>" data-contar="60">
        <small data-contador></small>
      </label>
      <label class="campo">Descrição (resumo no Google)
        <textarea name="descricao" rows="4" required maxlength="300" data-contar="155"><?= e($v('descricao', $post['descricao'])) ?></textarea>
        <small data-contador></small>
      </label>
      <label class="campo">Categorias <small>(separadas por vírgula)</small>
        <input type="text" name="categorias" value="<?= e($v('categorias', implode(', ', $post['categorias']))) ?>" placeholder="Dicas, Exames, Saúde">
      </label>
      <label class="campo">Palavras-chave <small>(separadas por vírgula)</small>
        <input type="text" name="palavras_chave" value="<?= e($v('palavras_chave', implode(', ', $post['palavras_chave']))) ?>" placeholder="endoscopia, gastrite">
      </label>
    </fieldset>

    <fieldset class="cartao-painel">
      <legend>Imagem de capa</legend>
      <?php if (!empty($post['capa'])): ?>
        <?= imagem_capa($post['capa'], 'Capa atual', ['larguras' => [360], 'sizes' => '320px', 'classe' => 'capa-atual']) ?>
        <label class="campo campo--linha"><input type="checkbox" name="remover_capa" value="1"> Remover a capa</label>
      <?php endif; ?>
      <label class="campo"><?= empty($post['capa']) ? 'Enviar imagem' : 'Trocar imagem' ?>
        <input type="file" name="capa" accept="image/jpeg,image/png,image/webp" data-capa>
        <small>JPG, PNG ou WebP, a partir de 600 px de largura (ideal 1200 × 800). É convertida automaticamente.</small>
      </label>
      <label class="campo">Descrição da imagem <small>(acessibilidade)</small>
        <input type="text" name="capa_alt" maxlength="160" value="<?= e($v('capa_alt', $post['capa']['alt'] ?? '')) ?>" placeholder="Ex.: Médica explicando o exame a uma paciente">
      </label>
    </fieldset>
  </aside>
</form>

<?php if (!$novo): ?>
  <form method="post" action="/admin/?acao=excluir" class="excluir" data-confirmar="Mover este artigo para a lixeira? Ele sai do site, mas fica guardado em conteudo/lixeira/.">
    <?= campo_csrf() ?>
    <input type="hidden" name="slug" value="<?= e($post['slug']) ?>">
    <button type="submit" class="link-botao link-botao--perigo">Excluir artigo</button>
  </form>
<?php endif; ?>
<?php require __DIR__ . '/_rodape.php'; ?>
