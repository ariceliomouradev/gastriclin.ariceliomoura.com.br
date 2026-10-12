"""Teste ponta a ponta do painel /admin/ no servidor local (php -S 127.0.0.1:8080 ...).
Uso: python v2/ferramentas/testar_painel.py <arquivo-credenciais.env> <imagem-de-teste.jpg>
O arquivo de credenciais tem linhas SENHA_admin=... e SENHA_editor=...
"""
import http.cookiejar
import os
import re
import sys
import urllib.error
import urllib.parse
import urllib.request
import uuid

BASE = 'http://127.0.0.1:8080'
cred = dict(l.strip().split('=', 1) for l in open(sys.argv[1]) if '=' in l)
IMAGEM = sys.argv[2]
falhas = []


def checar(condicao, descricao):
    print(('  ok   ' if condicao else '  FALHA') + ' ' + descricao)
    if not condicao:
        falhas.append(descricao)


class Navegador:
    def __init__(self):
        self.jar = http.cookiejar.CookieJar()
        self.op = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(self.jar))

    def get(self, caminho):
        try:
            r = self.op.open(BASE + caminho, timeout=30)
            return r.status, r.read().decode('utf-8', 'replace'), r.geturl()
        except urllib.error.HTTPError as e:
            return e.code, e.read().decode('utf-8', 'replace'), caminho

    def post(self, caminho, campos, arquivos=None):
        if arquivos:
            limite = uuid.uuid4().hex
            partes = []
            for k, v in campos.items():
                partes.append(f'--{limite}\r\nContent-Disposition: form-data; name="{k}"\r\n\r\n{v}\r\n'.encode())
            for k, (nome, dados, tipo) in arquivos.items():
                partes.append(f'--{limite}\r\nContent-Disposition: form-data; name="{k}"; filename="{nome}"\r\nContent-Type: {tipo}\r\n\r\n'.encode() + dados + b'\r\n')
            corpo = b''.join(partes) + f'--{limite}--\r\n'.encode()
            req = urllib.request.Request(BASE + caminho, data=corpo, headers={'Content-Type': f'multipart/form-data; boundary={limite}'})
        else:
            req = urllib.request.Request(BASE + caminho, data=urllib.parse.urlencode(campos).encode())
        try:
            r = self.op.open(req, timeout=60)
            return r.status, r.read().decode('utf-8', 'replace'), r.geturl()
        except urllib.error.HTTPError as e:
            return e.code, e.read().decode('utf-8', 'replace'), caminho

    def csrf(self, caminho):
        _, html, _ = self.get(caminho)
        m = re.search(r'name="csrf" value="([a-f0-9]+)"', html)
        return m.group(1) if m else ''

    def entrar(self, papel):
        token = self.csrf('/admin/?acao=entrar')
        return self.post('/admin/?acao=entrar', {'csrf': token, 'email': f'teste-{papel}@gastriclin.test', 'senha': cred[f'SENHA_{papel}']})


def avisos(html):
    return [m.strip() for m in re.findall(r'class="aviso-painel[^"]*"[^>]*>([^<]+)<', html)]


slug = 'artigo-de-teste-do-painel'
imagem = open(IMAGEM, 'rb').read()
conteudo = '<h2>Seção</h2><p>Texto com <b>negrito</b><script>alert(1)</script> e <a href="javascript:alert(2)">link ruim</a>.</p><ul><li>item</li></ul>'
base_form = {
    'slug_original': '', 'titulo': 'Artigo de teste do painel', 'slug': '', 'conteudo': conteudo,
    'descricao': 'Descrição de teste do artigo criado pelo painel.', 'publicado_em': '2026-10-12',
    'categorias': 'Dicas, Exames', 'palavras_chave': 'teste, painel', 'titulo_seo': '', 'capa_alt': 'Capa de teste',
}

print('1. Acesso sem login')
anon = Navegador()
status, html, url = anon.get('/admin/')
checar('acao=entrar' in url, 'painel redireciona para o login')
st, _, _ = anon.post('/admin/?acao=salvar', {'titulo': 'x'})
checar(st == 400, 'POST sem token CSRF é recusado (400)')

print('2. Equipe de TI cria rascunho')
ed = Navegador()
_, html, url = ed.entrar('editor')
checar(url.rstrip('/').endswith('/admin'), 'equipe de TI entra no painel')
token = ed.csrf('/admin/?acao=editar')
_, html, url = ed.post('/admin/?acao=salvar', {**base_form, 'csrf': token, 'botao': 'rascunho'}, {'capa': ('capa.jpg', imagem, 'image/jpeg')})
checar(any(a.startswith('Rascunho salvo') for a in avisos(html)), 'rascunho salvo')
checar(f'slug={slug}' in url, 'endereço gerado a partir do título')
st, _, _ = anon.get(f'/{slug}/')
checar(st == 404, 'rascunho não aparece no site')
st, html, _ = ed.get(f'/admin/?acao=previa&slug={slug}')
checar(st == 200 and 'Artigo de teste do painel' in html, 'pré-visualização funciona para quem está logado')
st2, html2, url2 = anon.get(f'/admin/?acao=previa&slug={slug}')
checar('acao=entrar' in url2, 'pré-visualização exige login')

print('3. Equipe de TI publica (revisão médica é opcional)')
token = ed.csrf(f'/admin/?acao=editar&slug={slug}')
form = {**base_form, 'slug_original': slug, 'slug': slug, 'csrf': token, 'botao': 'publicar'}
_, html, _ = ed.post('/admin/?acao=salvar', form)
checar('Artigo publicado no site.' in avisos(html), 'publica sem exigir revisão médica')

print('4. Artigo no site')
st, html, _ = anon.get(f'/{slug}/')
checar(st == 200, 'artigo publicado responde 200')
checar('<script>alert' not in html and 'javascript:' not in html, 'script e link perigoso foram removidos')
checar('<strong>negrito</strong>' in html, 'formatação preservada (negrito)')
checar('Revisado por' not in html, 'sem revisão informada, nada de "Revisado por"')
m = re.search(r'src="(/uploads/blog/[^"]+\.webp)"', html)
checar(bool(m), 'capa convertida para WebP')
if m:
    st, _, _ = anon.get(m.group(1))
    checar(st == 200, 'arquivo da capa acessível')
_, sitemap, _ = anon.get('/sitemap.xml')
checar(f'/{slug}/' in sitemap, 'artigo entra no sitemap')
_, blog, _ = anon.get('/blog/')
checar(f'/{slug}/' in blog, 'artigo aparece na lista do blog')

print('5. Revisão médica opcional e endereço fixo')
token = ed.csrf(f'/admin/?acao=editar&slug={slug}')
_, html, _ = ed.post('/admin/?acao=salvar', {**form, 'csrf': token, 'revisado': '1', 'revisor': 'Dr. Teste – CRM-CE 0000'})
_, html, _ = anon.get(f'/{slug}/')
checar('Revisado por Dr. Teste' in html, 'quando informada, a revisão aparece no artigo')
token = ed.csrf(f'/admin/?acao=editar&slug={slug}')
_, html, _ = ed.post('/admin/?acao=salvar', {**form, 'csrf': token, 'slug': 'outro-endereco'})
checar(any('não pode mudar' in a for a in avisos(html)), 'mudança de endereço de artigo publicado bloqueada')

print('6. Tirar do site e excluir')
token = ed.csrf(f'/admin/?acao=editar&slug={slug}')
ed.post('/admin/?acao=salvar', {**form, 'csrf': token, 'botao': 'rascunho'})
st, _, _ = anon.get(f'/{slug}/')
checar(st == 404, '"Tirar do site" remove o artigo do site')
token = ed.csrf(f'/admin/?acao=editar&slug={slug}')
ed.post('/admin/?acao=excluir', {'csrf': token, 'slug': slug})
_, html, _ = ed.get('/admin/')
checar(slug not in html, 'equipe de TI exclui (vai para a lixeira)')

print('7. Usuários e tentativas de login')
st, html, url = ed.get('/admin/?acao=usuarios')
checar('acao=usuarios' not in url or 'Novo usuário' not in html, 'equipe de TI não acessa a gestão de usuários')
ad = Navegador()
ad.entrar('admin')
st, html, _ = ad.get('/admin/?acao=usuarios')
checar('Novo usuário' in html, 'administrador acessa a gestão de usuários')
bruto = Navegador()
for i in range(5):
    t = bruto.csrf('/admin/?acao=entrar')
    _, html, _ = bruto.post('/admin/?acao=entrar', {'csrf': t, 'email': 'teste-admin@gastriclin.test', 'senha': 'errada' + str(i)})
t = bruto.csrf('/admin/?acao=entrar')
_, html, _ = bruto.post('/admin/?acao=entrar', {'csrf': t, 'email': 'teste-admin@gastriclin.test', 'senha': cred['SENHA_admin']})
checar('Muitas tentativas' in html, 'login bloqueado após 5 senhas erradas (mesmo com a senha certa)')

print(f'\n{"TUDO OK" if not falhas else str(len(falhas)) + " FALHA(S)"}')
sys.exit(1 if falhas else 0)
