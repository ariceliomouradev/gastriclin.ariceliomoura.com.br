<?php
// Limpa o HTML vindo do editor do painel: só formatação de texto passa (sem scripts, estilos ou atributos perigosos).

declare(strict_types=1);

const TAGS_PERMITIDAS = ['p', 'h2', 'h3', 'h4', 'strong', 'em', 'u', 'ul', 'ol', 'li', 'a', 'blockquote', 'br'];
// Equivalências de tags que os navegadores geram no editor
const TAGS_EQUIVALENTES = ['b' => 'strong', 'i' => 'em', 'h1' => 'h2', 'h5' => 'h4', 'h6' => 'h4', 'div' => 'p'];

function html_seguro(string $html): string
{
    $html = trim($html);
    if ($html === '') return '';

    $doc = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8"><div id="raiz">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
    libxml_clear_errors();

    $raiz = $doc->getElementById('raiz');
    if (!$raiz) return '';
    limpar_no($raiz, $doc);

    $saida = '';
    foreach (iterator_to_array($raiz->childNodes) as $filho) $saida .= $doc->saveHTML($filho);

    // Texto solto no nível de cima vira parágrafo; remove parágrafos vazios
    $saida = preg_replace('#<p>(\s|&nbsp;|<br>)*</p>#u', '', $saida);
    return trim((string) $saida);
}

function limpar_no(DOMNode $no, DOMDocument $doc): void
{
    foreach (iterator_to_array($no->childNodes) as $filho) {
        if ($filho instanceof DOMText) continue;
        if (!$filho instanceof DOMElement) {
            $no->removeChild($filho); // comentários, instruções etc.
            continue;
        }
        $tag = strtolower($filho->tagName);
        if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'svg', 'math', 'img', 'video', 'audio', 'link', 'meta'], true)) {
            $no->removeChild($filho);
            continue;
        }
        $tag = TAGS_EQUIVALENTES[$tag] ?? $tag;
        limpar_no($filho, $doc);

        if (!in_array($tag, TAGS_PERMITIDAS, true)) {
            // Tag não permitida (span, font...): mantém só o conteúdo
            while ($filho->firstChild) $no->insertBefore($filho->firstChild, $filho);
            $no->removeChild($filho);
            continue;
        }

        $novo = $doc->createElement($tag);
        while ($filho->firstChild) $novo->appendChild($filho->firstChild);
        if ($tag === 'a') {
            $href = trim((string) $filho->getAttribute('href'));
            if (!preg_match('#^(https?://|mailto:|tel:|/(?!/)|\#)#i', $href)) {
                // Link sem endereço seguro: mantém só o texto
                while ($novo->firstChild) $no->insertBefore($novo->firstChild, $filho);
                $no->removeChild($filho);
                continue;
            }
            $novo->setAttribute('href', $href);
            if (preg_match('#^https?://#i', $href) && !str_contains($href, 'gastriclin.com.br')) {
                $novo->setAttribute('target', '_blank');
                $novo->setAttribute('rel', 'noopener');
            }
        }
        $no->replaceChild($novo, $filho);
    }
}
