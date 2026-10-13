"""Verifica o site inteiro num servidor PHP rodando (local ou no GitHub Actions).
Uso: python ferramentas/verificar_site.py [http://127.0.0.1:8080]

Falha (código de saída 1) se encontrar: erro de PHP, página fora do ar, palavras coladas, título/descrição/H1 ausentes,
H1 duplicado, títulos pulando nível, JSON-LD inválido, imagem sem alt, link interno quebrado, página sem botão de
agendamento ou pasta interna acessível pela web.
"""
import html as htmllib
import json
import re
import sys
import urllib.error
import urllib.request

BASE = (sys.argv[1] if len(sys.argv) > 1 else 'http://127.0.0.1:8080').rstrip('/')
PERMITIDAS = re.compile(r'GastriClin|WhatsApp|SulAmérica|Doctoralia|YouTube|LinkedIn|pHmetria')
BLOCOS = 'p|div|section|article|header|footer|main|nav|aside|li|ul|ol|h[1-6]|br|tr|td|th|summary|details|address|blockquote|figure|button|time|small|title'


class SemRedirecionar(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, *a, **k):
        return None


abrir = urllib.request.build_opener(SemRedirecionar).open


def baixar(caminho):
    try:
        r = abrir(BASE + caminho, timeout=30)
        return r.status, r.read().decode('utf-8', 'replace')
    except urllib.error.HTTPError as e:
        return e.code, e.read().decode('utf-8', 'replace')


def texto_visivel(h):
    h = re.sub(r'<(script|style|svg|template)\b[\s\S]*?</\1>', ' ', h, flags=re.I)
    h = re.sub(rf'</?({BLOCOS})\b[^>]*>', '\n', h, flags=re.I)
    return htmllib.unescape(re.sub(r'<[^>]+>', '', h))


erros, avisos = [], []
status, sitemap = baixar('/sitemap.xml')
if status != 200:
    print('sitemap.xml fora do ar:', status)
    sys.exit(1)
paginas = [re.sub(r'^https?://[^/]+', '', u) for u in re.findall(r'<loc>([^<]+)</loc>', sitemap)] + ['/bio/']
status_link = {}

for caminho in paginas:  # a lista cresce com as páginas do blog encontradas pelo caminho (/blog/pagina/N/)
    st, h = baixar(caminho)
    if st != 200:
        erros.append(f'{caminho}: HTTP {st}')
        continue
    if re.search(r'(Fatal error|Warning|Notice|Deprecated|Parse error)\b.{0,200}on line', h):
        erros.append(f'{caminho}: erro de PHP → ' + re.search(r'(Fatal error|Warning|Notice|Deprecated|Parse error).{0,150}', h).group(0))
    texto = texto_visivel(h)
    for m in re.finditer(r'\S{0,20}[a-zà-ú)”"][.,;:!?…][A-Za-zÀ-ú“"(]\S{0,20}', texto):
        if not re.search(r'https?:|www\.|\.com|\.br|@|wa\.me', m.group(0)):
            erros.append(f'{caminho}: pontuação sem espaço → "{m.group(0)}"')
    for m in re.finditer(r'\S{0,15}(?<![A-Za-zÀ-ú])[a-zà-ú]{1,3}[A-ZÀ-Ú][a-zà-ú]+\S{0,10}|\S{0,15}[a-zà-ú]{2,}[A-ZÀ-Ú][a-zà-ú]{2,}\S{0,10}', texto):
        if not PERMITIDAS.search(m.group(0)):
            erros.append(f'{caminho}: palavras coladas → "{m.group(0)}"')
    niveis = [int(n) for n in re.findall(r'<h([1-6])[\s>]', re.sub(r'<(svg|script)\b[\s\S]*?</\1>', '', h))]
    if niveis.count(1) != 1:
        erros.append(f'{caminho}: {niveis.count(1)} títulos H1')
    for a, b in zip(niveis, niveis[1:]):
        if b > a + 1:
            erros.append(f'{caminho}: H{b} logo após H{a}')
    titulo = (re.search(r'<title>([^<]*)</title>', h) or [None, ''])[1]
    desc = (re.search(r'<meta name="description" content="([^"]*)"', h) or [None, ''])[1]
    if not titulo or not desc:
        erros.append(f'{caminho}: sem <title> ou meta description')
    if len(htmllib.unescape(titulo)) > 65:
        avisos.append(f'{caminho}: título com {len(titulo)} caracteres')
    for j in re.findall(r'<script type="application/ld\+json">([\s\S]*?)</script>', h):
        try:
            json.loads(j)
        except ValueError as e:
            erros.append(f'{caminho}: JSON-LD inválido ({e})')
    for img in re.findall(r'<img\b[^>]*>', h):
        if not re.search(r'\salt=', img):
            erros.append(f'{caminho}: imagem sem alt')
    for pag in re.findall(r'href="(/blog/pagina/\d+/)"', h):
        if pag not in paginas:
            paginas.append(pag)
    if 'data-cta="doctoralia"' not in h or 'data-cta="whatsapp"' not in h:
        erros.append(f'{caminho}: falta botão de agendamento')
    for href in set(re.findall(r'href="(/[^"#?]*)"', h)):
        if href.startswith('/assets/') or re.search(r'\.[a-z0-9]{2,5}$', href):
            continue
        if href not in status_link:
            status_link[href] = baixar(href)[0]
        if status_link[href] not in (200, 301, 302):
            erros.append(f'{caminho}: link interno quebrado → {href} ({status_link[href]})')

# Pastas internas não podem ser acessadas pela web
for caminho in ['/app/config.php', '/app/dados/medicos.php', '/conteudo/usuarios.json', '/dados-iniciais/blog/', '/ferramentas/criar_usuario.php', '/.git/config', '/README.md']:
    st, _ = baixar(caminho)
    if st not in (403, 404):
        erros.append(f'{caminho}: deveria estar bloqueado, respondeu {st}')

# Página inexistente e página do blog além da última
for caminho in ['/pagina-que-nao-existe/', '/blog/pagina/999/']:
    st, _ = baixar(caminho)
    if st != 404:
        erros.append(f'{caminho}: deveria responder 404, respondeu {st}')
if not any(p.startswith('/blog/pagina/') for p in paginas):
    avisos.append('nenhuma página de paginação do blog encontrada')

print(f'{len(paginas)} páginas verificadas, {len(status_link)} links internos conferidos')
if avisos:
    print(f'\n⚠ {len(avisos)} aviso(s):\n  ' + '\n  '.join(sorted(set(avisos))))
if erros:
    print(f'\n❌ {len(erros)} erro(s):\n  ' + '\n  '.join(sorted(set(erros))))
    sys.exit(1)
print('\n✅ Nenhum erro encontrado.')
