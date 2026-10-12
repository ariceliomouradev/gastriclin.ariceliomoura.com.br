// Painel GastriClin: editor de texto, contadores de caracteres e confirmações.
(function () {
  // ---------- Editor visual ----------
  var form = document.querySelector('[data-editor-form]');
  var area = document.querySelector('[data-area]');
  var saida = document.querySelector('[data-conteudo]');
  if (form && area && saida) {
    document.execCommand('defaultParagraphSeparator', false, 'p');

    document.querySelectorAll('[data-barra] [data-cmd]').forEach(function (botao) {
      botao.addEventListener('mousedown', function (e) { e.preventDefault(); }); // mantém a seleção no texto
      botao.addEventListener('click', function () {
        var cmd = botao.dataset.cmd;
        var valor = botao.dataset.valor || null;
        if (cmd === 'createLink') {
          valor = window.prompt('Endereço do link (ex.: https://... ou /endoscopia/):', 'https://');
          if (!valor || valor === 'https://') return;
        }
        if (cmd === 'formatBlock') valor = '<' + valor + '>';
        document.execCommand(cmd, false, valor);
        area.focus();
      });
    });

    // Colar sempre como texto simples: cada linha vira um parágrafo
    area.addEventListener('paste', function (e) {
      e.preventDefault();
      var texto = (e.clipboardData || window.clipboardData).getData('text/plain');
      var html = texto
        .split(/\r?\n\s*\r?\n|\r?\n/)
        .map(function (l) { return l.trim(); })
        .filter(Boolean)
        .map(function (l) {
          var esc = l.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
          return '<p>' + esc + '</p>';
        })
        .join('');
      document.execCommand('insertHTML', false, html);
    });

    form.addEventListener('submit', function () {
      saida.value = area.innerHTML;
    });

    // Avisa antes de sair com alterações não salvas
    var alterado = false;
    form.addEventListener('input', function () { alterado = true; });
    form.addEventListener('submit', function () { alterado = false; });
    window.addEventListener('beforeunload', function (e) {
      if (alterado) { e.preventDefault(); e.returnValue = ''; }
    });
  }

  // ---------- Endereço gerado a partir do título ----------
  var titulo = document.querySelector('[data-titulo]');
  var slug = document.querySelector('[data-slug]');
  if (titulo && slug && !slug.readOnly) {
    var manual = slug.value !== '';
    slug.addEventListener('input', function () { manual = true; });
    titulo.addEventListener('input', function () {
      if (manual) return;
      slug.value = titulo.value
        .normalize('NFD').replace(/[̀-ͯ]/g, '')
        .toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    });
  }

  // ---------- Contadores de caracteres (Google) ----------
  document.querySelectorAll('[data-contar]').forEach(function (campo) {
    var limite = Number(campo.dataset.contar);
    var aviso = campo.parentElement.querySelector('[data-contador]');
    function atualizar() {
      var n = campo.value.length;
      aviso.textContent = n + ' de ' + limite + ' caracteres' + (n > limite ? ' – o Google vai cortar o texto' : '');
      aviso.classList.toggle('passou', n > limite);
    }
    campo.addEventListener('input', atualizar);
    atualizar();
  });

  // ---------- Confirmação antes de excluir ----------
  document.querySelectorAll('form[data-confirmar]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      if (!window.confirm(f.dataset.confirmar)) e.preventDefault();
    });
  });
})();
