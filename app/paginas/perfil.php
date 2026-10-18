<?php
// Perfil de profissional (/dr-.../, /valdeline-sousa/). Recebe $pessoa do roteador.
$p = $pessoa;
$servicos_pessoa = array_values(array_filter(servicos(), fn($s) => in_array($s['slug'], $p['servicos'], true)));
$cargo_curto = explode(' · ', $p['cargo'])[0];

$schema = [
    '@context' => 'https://schema.org',
    '@type' => $p['medico'] ? 'Physician' : 'Person',
    'name' => nome_exibicao($p),
    'jobTitle' => $p['cargo'],
    'url' => SITE['url'] . "/{$p['slug']}/",
    'image' => SITE['url'] . "/assets/img/{$p['foto']}-" . max(manifesto_imagens()[$p['foto']]['larguras']) . '.webp?v=' . (manifesto_imagens()[$p['foto']]['v'] ?? ''),
    'worksFor' => ['@id' => SITE['url'] . '/#clinica'],
];
if ($p['medico']) {
    $schema['medicalSpecialty'] = 'Gastroenterologic';
    $schema['identifier'] = $p['registro'] ?? '';
}

$pagina = [
    'titulo' => titulo_com_marca("{$p['nome_curto']} – {$cargo_curto}"),
    'descricao' => limitar("{$p['nome_curto']}, " . mb_strtolower($cargo_curto) . " na GastriClin, em Fortaleza. {$p['resumo']} Agende online ou pelo WhatsApp."),
    'tipo' => 'profile',
    'json_ld' => [$schema],
];
require APP . '/layout/inicio.php';
?>
  <section class="perfil-topo fundo-escuro">
    <div class="container perfil-topo__grade">
      <div class="perfil-topo__foto">
        <?= imagem($p['foto'], 'Foto de ' . nome_exibicao($p), ['sizes' => '(max-width: 860px) 70vw, 380px', 'carregar' => 'eager']) ?>
      </div>
      <div>
        <?php componente('trilha', ['itens' => [['rotulo' => 'Corpo clínico', 'href' => '/clinico/'], ['rotulo' => $p['nome_curto'], 'href' => "/{$p['slug']}/"]]]); ?>
        <p class="sobretitulo"><?= $p['medico'] ? 'Corpo clínico' : 'Gestão' ?></p>
        <h1><?= e(nome_exibicao($p)) ?></h1>
        <p class="perfil-topo__cargo"><?= e($p['cargo']) ?></p>
        <?php if (!empty($p['registro'])): ?><p class="perfil-topo__registro"><?= e($p['registro']) ?></p><?php endif; ?>
        <?php if ($p['medico']) componente('botoes-agendar', ['origem' => "perfil-{$p['slug']}", 'servico' => "Consulta com {$p['nome_curto']}"]); ?>
      </div>
    </div>
  </section>

  <section class="secao secao--branca">
    <div class="container perfil-corpo">
      <div class="prosa">
        <?php foreach ($p['bio'] as $par): ?><p><?= e($par) ?></p><?php endforeach; ?>
        <?php if (!empty($p['formacao'])): ?>
          <h2>Formação</h2>
          <ul>
            <?php foreach ($p['formacao'] as $f): ?><li><?= e($f) ?></li><?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <?php if (!empty($p['sociedades'])): ?>
          <p class="perfil-sociedade"><?= icone('escudo', 20) ?> Membro da <?= e(implode(', ', $p['sociedades'])) ?></p>
        <?php endif; ?>
        <?php if (!empty($p['frase'])): ?><blockquote>“<?= e($p['frase']) ?>”</blockquote><?php endif; ?>
      </div>
      <aside class="perfil-areas" aria-label="Áreas de atuação">
        <h2>Áreas de atuação</h2>
        <ul class="lista-check">
          <?php foreach ($p['areas'] as $a): ?><li><?= icone('check', 20) ?> <?= e($a) ?></li><?php endforeach; ?>
        </ul>
      </aside>
    </div>
  </section>

  <?php if ($servicos_pessoa): ?>
    <section class="secao">
      <div class="container">
        <div class="cabecalho-secao">
          <p class="sobretitulo">Na GastriClin</p>
          <h2>Atendimentos com <em><?= e($p['nome_curto']) ?></em></h2>
        </div>
        <div class="grade <?= count($servicos_pessoa) >= 3 ? 'grade--3' : 'grade--2' ?>">
          <?php foreach ($servicos_pessoa as $s) componente('card-servico', ['servico' => $s, 'origem' => "perfil-{$p['slug']}"]); ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
<?php require APP . '/layout/fim.php'; ?>
