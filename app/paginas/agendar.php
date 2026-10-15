<?php
// /agendar/ – agenda online da Doctoralia embutida no site (widget oficial "calendário da clínica").
// O widget é um <iframe> da Doctoralia: escolha de especialidade → serviço → convênio → médico → horário.
// A confirmação da reserva é concluída na Doctoralia (nova aba); este site não recebe dados do paciente.
$pagina = [
    'titulo' => 'Agendar consulta ou exame online em Fortaleza | GastriClin',
    'descricao' => 'Veja os horários disponíveis e agende sua consulta ou exame na GastriClin, em Fortaleza, pela agenda online. Atendemos convênios.',
    'sem_barra' => true, // nesta página a barra fixa do celular só atrapalharia a agenda
];
require APP . '/layout/inicio.php';

componente('topo-pagina', [
    'trilha' => [['rotulo' => 'Agendar', 'href' => '/agendar/']],
    'sobretitulo' => 'Agendamento online',
    'titulo_html' => 'Escolha o <em>médico e o horário</em>',
    'lead' => 'Veja os horários disponíveis e agende sua consulta ou exame na GastriClin.',
]);
$end = SITE['endereco'];
?>
  <section class="secao">
    <div class="container agendar">

      <div class="agendar__agenda">
        <div class="agenda" data-agenda>
          <div class="agenda__carregando" data-agenda-carregando role="status">
            <span class="agenda__spinner" aria-hidden="true"></span>
            <span>Carregando a agenda…</span>
          </div>

          <!-- Código oficial da Doctoralia. O script dela troca este link pelo calendário (iframe). -->
          <a href="<?= e(SITE['links']['doctoralia']) ?>" data-zl-widget-facility="gastriclin" rel="nofollow" data-placement="inline" data-zlw-type="facility-calendar-listing-with-saas-only" data-zlw-a11y-title="Agenda online da GastriClin na Doctoralia" tabindex="-1" aria-hidden="true">Marque uma consulta</a>

          <div class="agenda__falha" data-agenda-falha hidden>
            <p><strong>Não conseguimos carregar a agenda aqui.</strong> Você pode agendar direto na Doctoralia ou falar com a nossa equipe.</p>
            <div class="botoes-agendar">
              <a class="btn btn--agendar" href="<?= e(SITE['links']['doctoralia']) ?>" target="_blank" rel="noopener" data-cta="doctoralia" data-origem="agendar-falha">
                <?= icone('calendario') ?> Abrir agenda na Doctoralia <span class="visualmente-oculto">(nova aba)</span>
              </a>
              <a class="btn btn--whatsapp" href="<?= e(whatsapp_url(mensagem_agendamento())) ?>" target="_blank" rel="noopener" data-cta="whatsapp" data-origem="agendar-falha">
                <?= icone('whatsapp') ?> Agendar pelo WhatsApp <span class="visualmente-oculto">(nova aba)</span>
              </a>
            </div>
          </div>

          <noscript>
            <div class="agenda__falha">
              <p><strong>Ative o JavaScript para ver a agenda aqui.</strong> Ou agende direto na Doctoralia:</p>
              <a class="btn btn--agendar" href="<?= e(SITE['links']['doctoralia']) ?>" target="_blank" rel="noopener"><?= icone('calendario') ?> Abrir a agenda na Doctoralia</a>
            </div>
          </noscript>
        </div>
      </div>

      <div class="agendar__info">
        <p class="sobretitulo">Passo a passo</p>
        <h2>Como <em>funciona</em></h2>
        <ol class="agendar__passos">
          <li><strong>Escolha a especialidade e o serviço.</strong> Consulta ou exame, com o médico que preferir.</li>
          <li><strong>Informe seu convênio.</strong> Se não tiver plano, escolha “Estou agendando sem plano de saúde”.</li>
          <li><strong>Escolha o horário.</strong> A confirmação é concluída na Doctoralia, a plataforma que a clínica usa para a agenda.</li>
        </ol>

        <aside class="cartao-agendar" aria-label="Atendimento pelo WhatsApp">
          <p class="cartao-agendar__titulo">Prefere conversar?</p>
          <p>Nossa equipe tira suas dúvidas e ajuda a agendar pelo WhatsApp.</p>
          <a class="btn btn--whatsapp" href="<?= e(whatsapp_url(mensagem_agendamento())) ?>" target="_blank" rel="noopener" data-cta="whatsapp" data-origem="agendar-lateral">
            <?= icone('whatsapp') ?> Falar pelo WhatsApp <span class="visualmente-oculto">(nova aba)</span>
          </a>
          <hr>
          <p class="cartao-agendar__local">
            <?= icone('local', 20) ?>
            <span><?= e("{$end['rua']} – {$end['bairro']}, {$end['cidade']}") ?></span>
          </p>
          <a href="/convenios/" class="link-seta">Ver convênios aceitos <?= icone('seta', 18) ?></a>
        </aside>

        <p class="agendar__privacidade">
          A agenda é exibida pela Doctoralia, que pode usar cookies conforme a política dela. Saiba mais na nossa
          <a href="/privacidade/">política de privacidade</a>.
        </p>
      </div>

    </div>
  </section>

  <script>
    // Carrega o script da Doctoralia depois que a página termina de carregar (não atrasa o resto do site)
    // e mostra o plano B se a agenda não aparecer.
    (function () {
      var caixa = document.querySelector('[data-agenda]');
      if (!caixa) return;
      var carregando = caixa.querySelector('[data-agenda-carregando]');
      var falha = caixa.querySelector('[data-agenda-falha]');
      var terminou = false;

      function mostrarFalha() {
        if (terminou) return;
        terminou = true;
        if (carregando) carregando.hidden = true;
        if (falha) falha.hidden = false;
        caixa.classList.add('agenda--falha'); // sem a altura reservada do calendário
      }
      function pronta(iframe) {
        if (terminou) return;
        terminou = true;
        var esconder = function () { if (carregando) carregando.hidden = true; };
        iframe.addEventListener('load', esconder);
        setTimeout(esconder, 1500);
      }

      // O widget insere o <iframe> dentro de .agenda: observa a chegada dele
      new MutationObserver(function (itens, obs) {
        var f = caixa.querySelector('iframe');
        if (f) { obs.disconnect(); pronta(f); }
      }).observe(caixa, { childList: true });

      function carregar() {
        var s = document.createElement('script');
        s.id = 'zl-facility-widget';
        s.async = true;
        s.src = 'https://www.doctoralia.com.br/platform/js/widget.js';
        s.onerror = mostrarFalha;
        document.body.appendChild(s);
        setTimeout(function () { if (!caixa.querySelector('iframe')) mostrarFalha(); }, 12000);
      }
      if (document.readyState === 'complete') carregar();
      else window.addEventListener('load', carregar);
    })();
  </script>
<?php require APP . '/layout/fim.php'; ?>
