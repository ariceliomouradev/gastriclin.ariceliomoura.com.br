<?php
// Cabeçalho com menu (submenus de Consultas e Exames) e botões de agendamento.
$atual = caminho_atual();
$ativo = fn(string $href) => $href === '/' ? $atual === '/' : str_starts_with($atual, $href);

$itens_menu = fn(array $lista, string $todosHref, string $todosRotulo) => array_merge(
    array_map(fn($s) => ['href' => "/{$s['slug']}/", 'rotulo' => $s['nome'], 'descricao' => $s['resumo'], 'icone' => $s['icone']], $lista),
    [['href' => $todosHref, 'rotulo' => $todosRotulo, 'descricao' => '', 'icone' => '']]
);
$menus = [
    ['id' => 'consultas', 'rotulo' => 'Consultas', 'itens' => $itens_menu(consultas(), '/especialidades/', 'Todas as especialidades')],
    ['id' => 'exames', 'rotulo' => 'Exames', 'itens' => $itens_menu(exames(), '/exames/', 'Todos os exames e procedimentos')],
];
$links = [
    ['href' => '/clinico/', 'rotulo' => 'Corpo clínico'],
    ['href' => '/convenios/', 'rotulo' => 'Convênios'],
    ['href' => '/sobre/', 'rotulo' => 'A clínica'],
    ['href' => '/blog/', 'rotulo' => 'Blog'],
];
?>
<header class="topo" data-topo>
  <div class="container topo__barra">
    <a href="/" class="topo__logo" aria-label="GastriClin – página inicial">
      <?= imagem('marca/gastriclin-roxo', 'GastriClin – Centro de Medicina Digestiva', ['altura' => 46, 'carregar' => 'eager', 'prioridade' => true]) ?>
    </a>

    <button class="topo__menu-btn" type="button" aria-expanded="false" aria-controls="menu-principal" data-menu-btn>
      <?= icone('menu', 24, 'icone-abrir') ?>
      <?= icone('fechar', 24, 'icone-fechar') ?>
      <span class="visualmente-oculto">Menu</span>
    </button>

    <nav id="menu-principal" class="topo__nav" aria-label="Principal" data-menu>
      <ul class="nav__lista">
        <?php foreach ($menus as $menu):
            $grupo_ativo = (bool) array_filter($menu['itens'], fn($i) => str_starts_with($atual, $i['href'])); ?>
          <li class="nav__item nav__item--sub" data-sub>
            <button class="nav__link nav__toggle<?= $grupo_ativo ? ' is-ativo' : '' ?>" type="button" aria-expanded="false" aria-controls="sub-<?= $menu['id'] ?>">
              <?= e($menu['rotulo']) ?>
              <?= icone('chevron-baixo', 18) ?>
            </button>
            <span class="nav__grupo" aria-hidden="true"><?= e($menu['rotulo']) ?></span>
            <ul class="nav__sub" id="sub-<?= $menu['id'] ?>">
              <?php foreach ($menu['itens'] as $item): ?>
                <li>
                  <a href="<?= e($item['href']) ?>" class="sub__link<?= $item['icone'] ? '' : ' sub__link--todos' ?>"<?= $atual === $item['href'] ? ' aria-current="page"' : '' ?>>
                    <?php if ($item['icone']): ?>
                      <span class="sub__icone"><?= icone($item['icone'], 22) ?></span>
                    <?php endif; ?>
                    <span>
                      <strong><?= e($item['rotulo']) ?></strong>
                      <?php if ($item['descricao']): ?><small><?= e($item['descricao']) ?></small><?php endif; ?>
                    </span>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </li>
        <?php endforeach; ?>
        <?php foreach ($links as $l): ?>
          <li class="nav__item">
            <a href="<?= $l['href'] ?>" class="nav__link<?= $ativo($l['href']) ? ' is-ativo' : '' ?>"<?= $ativo($l['href']) ? ' aria-current="page"' : '' ?>><?= e($l['rotulo']) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="topo__acoes">
        <a class="btn btn--agendar btn--pequeno" href="/agendar/" data-cta="doctoralia" data-origem="menu">
          <?= icone('calendario') ?>
          Agendar<span class="visualmente-oculto"> online</span>
        </a>
        <a class="btn btn--whatsapp btn--pequeno topo__whats" href="<?= e(whatsapp_url()) ?>" target="_blank" rel="noopener" data-cta="whatsapp" data-origem="menu">
          <?= icone('whatsapp') ?>
          <span class="topo__whats-texto">WhatsApp</span>
          <span class="visualmente-oculto"> (abre em nova aba)</span>
        </a>
      </div>
    </nav>
  </div>
</header>
