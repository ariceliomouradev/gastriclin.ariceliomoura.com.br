<?php
// /privacidade/ – PENDENTE: texto-base. Precisa de revisão jurídica e dos dados do encarregado (DPO) antes de publicar.
$end = SITE['endereco'];
$pagina = [
    'titulo' => 'Política de Privacidade | GastriClin',
    'descricao' => 'Como a GastriClin trata os dados pessoais de pacientes e visitantes do site, conforme a LGPD.',
];
require APP . '/layout/inicio.php';

componente('topo-pagina', [
    'trilha' => [['rotulo' => 'Política de privacidade', 'href' => '/privacidade/']],
    'sobretitulo' => 'LGPD',
    'titulo_html' => 'Política de <em>privacidade</em>',
]);
?>
  <section class="secao secao--branca">
    <div class="container prosa">
      <p><strong>Última atualização:</strong> [PENDENTE – data da publicação]</p>

      <h2>1. Quem somos</h2>
      <p>
        GastriClin – Centro de Medicina Digestiva, CNPJ <?= e(SITE['cnpj']) ?>, com endereço na <?= e("{$end['rua']}, {$end['complemento']}, {$end['bairro']}, {$end['cidade']} – {$end['uf']}") ?>.
      </p>

      <h2>2. Quais dados este site coleta</h2>
      <p>
        Este site não possui formulários e não coleta dados pessoais diretamente. Ao clicar em “Agendar online”, você é direcionado à plataforma Doctoralia; ao
        clicar em “WhatsApp”, a conversa acontece no aplicativo WhatsApp. Cada plataforma tem sua própria política de privacidade.
      </p>
      <p>
        Com o seu consentimento, usamos cookies de medição (Google Analytics) para entender, de forma agregada, como o site é usado. Você pode recusar esses cookies
        no aviso exibido na primeira visita.
      </p>

      <h2>3. Dados tratados no atendimento</h2>
      <p>
        Os dados informados no agendamento e no atendimento (incluindo dados de saúde) são tratados para a prestação do serviço de saúde, o cumprimento de obrigações
        legais e regulatórias e a tutela da saúde, conforme os artigos 7º e 11 da Lei nº 13.709/2018 (LGPD).
      </p>

      <h2>4. Seus direitos</h2>
      <p>
        Você pode solicitar a confirmação do tratamento, o acesso, a correção, a anonimização, a portabilidade ou a exclusão dos seus dados, nos limites da lei e das
        normas de guarda de prontuário.
      </p>

      <h2>5. Contato do encarregado (DPO)</h2>
      <p>[PENDENTE – nome e e-mail do encarregado de dados]. Enquanto isso: <a href="mailto:<?= e(SITE['email']) ?>"><?= e(SITE['email']) ?></a>.</p>
    </div>
  </section>
<?php require APP . '/layout/fim.php'; ?>
