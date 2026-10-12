// GastriClin – comportamento do site (menu, cliques de agendamento e consentimento de cookies).
(function () {
  // ---------------- Cabeçalho e menu ----------------
  var topo = document.querySelector('[data-topo]');
  var btn = document.querySelector('[data-menu-btn]');
  var menu = document.querySelector('[data-menu]');

  function aoRolar() {
    if (topo) topo.classList.toggle('is-rolado', window.scrollY > 8);
  }
  // Sem leitura de scrollY no carregamento: ela forçaria um cálculo de layout que trava a página por alguns ms.
  // O navegador dispara "scroll" ao restaurar uma página já rolada, então o estado se ajusta sozinho.
  window.addEventListener('scroll', aoRolar, { passive: true });

  function fecharMenu() {
    if (!btn || !menu) return;
    btn.setAttribute('aria-expanded', 'false');
    menu.classList.remove('is-aberto');
    document.body.style.overflow = '';
  }
  if (btn && menu) {
    btn.addEventListener('click', function () {
      var aberto = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', String(!aberto));
      menu.classList.toggle('is-aberto', !aberto);
      document.body.style.overflow = aberto ? '' : 'hidden';
    });
  }

  // Submenus no desktop: abre no clique e com teclado; fecha com Esc ou clique fora
  var subs = Array.prototype.slice.call(document.querySelectorAll('[data-sub]'));
  function fecharSubs(exceto) {
    subs.forEach(function (s) {
      if (s === exceto) return;
      s.classList.remove('is-aberto');
      var b = s.querySelector('button');
      if (b) b.setAttribute('aria-expanded', 'false');
    });
  }
  subs.forEach(function (sub) {
    var toggle = sub.querySelector('button');
    if (!toggle) return;
    toggle.addEventListener('click', function () {
      var aberto = sub.classList.toggle('is-aberto');
      toggle.setAttribute('aria-expanded', String(aberto));
      fecharSubs(sub);
    });
    sub.addEventListener('focusout', function (e) {
      if (!sub.contains(e.relatedTarget)) fecharSubs();
    });
  });
  document.addEventListener('click', function (e) {
    if (!subs.some(function (s) { return s.contains(e.target); })) fecharSubs();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    fecharSubs();
    if (menu && menu.classList.contains('is-aberto')) {
      fecharMenu();
      btn.focus();
    }
  });

  // ---------------- Analytics ----------------
  var gaId = document.body.getAttribute('data-ga4') || '';
  window.dataLayer = window.dataLayer || [];
  window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };

  // Evento de clique em "Agendar" (Doctoralia) e "WhatsApp", com a origem e o serviço
  document.addEventListener('click', function (e) {
    var el = e.target.closest && e.target.closest('[data-cta]');
    if (!el) return;
    var dados = { canal: el.dataset.cta, origem: el.dataset.origem || '', servico: el.dataset.servico || '' };
    window.dataLayer.push(Object.assign({ event: 'agendar_click' }, dados));
    if (gaId) window.gtag('event', 'agendar_click', dados);
  });

  if (!gaId) return;
  var CHAVE = 'gastriclin-consentimento';
  function ler() { try { return localStorage.getItem(CHAVE); } catch (e) { return null; } }
  function gravar(v) { try { localStorage.setItem(CHAVE, v); } catch (e) {} }
  function carregar() {
    var s = document.createElement('script');
    s.async = true;
    s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(gaId);
    document.head.appendChild(s);
    window.gtag('js', new Date());
    window.gtag('config', gaId, { anonymize_ip: true });
  }
  var banner = document.querySelector('[data-cookies]');
  var escolha = ler();
  if (escolha === 'aceito') carregar();
  else if (!escolha && banner) banner.hidden = false;
  if (banner) {
    banner.querySelector('[data-cookies-aceitar]').addEventListener('click', function () {
      gravar('aceito'); banner.hidden = true; carregar();
    });
    banner.querySelector('[data-cookies-recusar]').addEventListener('click', function () {
      gravar('recusado'); banner.hidden = true;
    });
  }
})();
