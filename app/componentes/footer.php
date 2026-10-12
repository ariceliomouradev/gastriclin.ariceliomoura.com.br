<?php
// Rodapé: chamada para agendar, navegação, contato e dados legais.
$rt = SITE['responsavel_tecnico'];
$end = SITE['endereco'];
?>
<footer class="rodape fundo-escuro">
  <div class="container">
    <div class="rodape__cta">
      <div>
        <p class="sobretitulo">Agende agora</p>
        <p class="rodape__cta-titulo">Cuidar da saúde digestiva começa com uma conversa.</p>
      </div>
      <?php componente('botoes-agendar', ['origem' => 'rodape']); ?>
    </div>

    <div class="rodape__grade">
      <div class="rodape__marca">
        <?= imagem('marca/gastriclin-branco', 'GastriClin – Centro de Medicina Digestiva', ['largura' => 220]) ?>
        <p>Consultas e exames do aparelho digestivo no mesmo lugar, com atendimento humanizado em Fortaleza.</p>
        <div class="rodape__redes">
          <a href="<?= e(SITE['links']['instagram']) ?>" target="_blank" rel="noopener" aria-label="Instagram da GastriClin"><?= icone('instagram') ?></a>
          <a href="<?= e(SITE['links']['facebook']) ?>" target="_blank" rel="noopener" aria-label="Facebook da GastriClin"><?= icone('facebook') ?></a>
          <a href="<?= e(whatsapp_url()) ?>" target="_blank" rel="noopener" aria-label="WhatsApp da GastriClin" data-cta="whatsapp" data-origem="rodape-redes"><?= icone('whatsapp') ?></a>
          <a href="mailto:<?= e(SITE['email']) ?>" aria-label="E-mail da GastriClin"><?= icone('email') ?></a>
        </div>
      </div>

      <nav aria-label="Consultas e exames">
        <p class="rodape__titulo">Consultas</p>
        <ul>
          <?php foreach (consultas() as $s): ?><li><a href="/<?= $s['slug'] ?>/"><?= e($s['nome']) ?></a></li><?php endforeach; ?>
        </ul>
        <p class="rodape__titulo">Exames</p>
        <ul>
          <?php foreach (exames() as $s): ?><li><a href="/<?= $s['slug'] ?>/"><?= e($s['nome']) ?></a></li><?php endforeach; ?>
        </ul>
      </nav>

      <nav aria-label="Institucional">
        <p class="rodape__titulo">A clínica</p>
        <ul>
          <li><a href="/sobre/">Sobre a GastriClin</a></li>
          <li><a href="/clinico/">Corpo clínico</a></li>
          <li><a href="/convenios/">Convênios</a></li>
          <li><a href="/blog/">Blog</a></li>
          <li><a href="/privacidade/">Política de privacidade</a></li>
        </ul>
      </nav>

      <div>
        <p class="rodape__titulo">Onde estamos</p>
        <address>
          <a href="<?= e(SITE['links']['google_maps']) ?>" target="_blank" rel="noopener" class="rodape__contato">
            <?= icone('local') ?>
            <span>
              <?= e($end['rua']) ?><br>
              <?= e($end['complemento']) ?><br>
              <?= e($end['bairro']) ?>, <?= e($end['cidade']) ?> – <?= e($end['uf']) ?><br>
              CEP <?= e($end['cep']) ?>
            </span>
          </a>
          <a href="<?= e(whatsapp_url()) ?>" target="_blank" rel="noopener" class="rodape__contato" data-cta="whatsapp" data-origem="rodape-contato">
            <?= icone('whatsapp') ?>
            <span><?= e(SITE['telefone']['exibicao']) ?></span>
          </a>
          <a href="mailto:<?= e(SITE['email']) ?>" class="rodape__contato">
            <?= icone('email') ?>
            <span><?= e(SITE['email']) ?></span>
          </a>
        </address>
        <?php if (SITE['horario']): ?>
          <ul class="rodape__horario">
            <?php foreach (SITE['horario'] as $h): ?><li><strong><?= e($h['dias']) ?></strong> <?= e($h['horas']) ?></li><?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>

    <div class="rodape__legal">
      <p>© <?= date('Y') ?> GastriClin – Centro de Medicina Digestiva · CNPJ <?= e(SITE['cnpj']) ?></p>
      <?php if ($rt): ?>
        <p>Responsável técnica: <?= e($rt['nome']) ?> – <?= e($rt['profissao']) ?><?= $rt['registro'] ? ' | ' . e($rt['registro']) : '' ?></p>
      <?php endif; ?>
    </div>
  </div>
</footer>
