    </main>
    <?php componente('footer'); ?>
    <?php if (empty($pagina['sem_barra'])) componente('barra-agendar', ['servico' => $pagina['servico'] ?? null]); ?>
    <!-- Script embutido (como na versão Node): sem requisição extra. Fonte editável: assets/js/site.js -->
    <script type="module"><?php readfile(PUBLICO . '/assets/js/site.js'); ?></script>
    <?php if (SITE['ga4_id']): ?>
      <div class="cookies" data-cookies hidden role="dialog" aria-label="Aviso de cookies" aria-live="polite">
        <p>
          Usamos cookies de medição para entender como o site é usado e melhorar o atendimento. Você pode aceitar ou recusar.
          <a href="/privacidade/">Saiba mais</a>.
        </p>
        <div class="cookies__acoes">
          <button type="button" class="btn btn--contorno btn--pequeno" data-cookies-recusar>Recusar</button>
          <button type="button" class="btn btn--agendar btn--pequeno" data-cookies-aceitar>Aceitar</button>
        </div>
      </div>
    <?php endif; ?>
  </body>
</html>
