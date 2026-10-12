<?php
// /sitemap.xml – gerado na hora: páginas fixas, serviços, perfis e artigos publicados (com data de atualização).
$urls = [];
foreach (['/', '/especialidades/', '/exames/', '/clinico/', '/convenios/', '/sobre/', '/blog/', '/privacidade/'] as $c) $urls[] = [$c, null];
foreach (servicos() as $s) $urls[] = ["/{$s['slug']}/", null];
foreach (profissionais_ativos() as $p) $urls[] = ["/{$p['slug']}/", null];
foreach (posts() as $p) $urls[] = ["/{$p['slug']}/", $p['atualizado_em'] ?: $p['publicado_em']];

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";
foreach ($urls as [$caminho, $data]) {
    echo '  <url><loc>', e(SITE['url'] . $caminho), '</loc>', $data ? '<lastmod>' . e(substr($data, 0, 10)) . '</lastmod>' : '', "</url>\n";
}
echo "</urlset>\n";
