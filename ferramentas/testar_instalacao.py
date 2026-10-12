"""Testa o primeiro acesso ao painel num servidor zerado (sem usuários) e prepara usuários de teste.
Uso: python ferramentas/testar_instalacao.py <raiz-do-site> <arquivo-credenciais-de-saida> [url-base]

1. /admin/ deve mandar para a tela de instalação
2. código errado é recusado; código certo (lido de conteudo/chave-instalacao.txt) cria o administrador
3. o administrador cria um usuário "Equipe de TI" pelo painel
As senhas geradas são gravadas no arquivo de credenciais (para o testar_painel.py) e nunca impressas.
"""
import http.cookiejar
import os
import re
import secrets
import sys
import urllib.error
import urllib.parse
import urllib.request

RAIZ, SAIDA = sys.argv[1], sys.argv[2]
BASE = (sys.argv[3] if len(sys.argv) > 3 else 'http://127.0.0.1:8080').rstrip('/')
falhas = []


def checar(cond, desc):
    print(('  ok   ' if cond else '  FALHA') + ' ' + desc)
    if not cond:
        falhas.append(desc)


jar = http.cookiejar.CookieJar()
op = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))


def get(c):
    try:
        r = op.open(BASE + c, timeout=30)
        return r.read().decode(), r.geturl()
    except urllib.error.HTTPError as e:
        return e.read().decode(), c


def post(c, campos):
    try:
        r = op.open(urllib.request.Request(BASE + c, data=urllib.parse.urlencode(campos).encode()), timeout=30)
        return r.read().decode(), r.geturl()
    except urllib.error.HTTPError as e:
        return e.read().decode(), c


def csrf(html):
    m = re.search(r'name="csrf" value="([a-f0-9]+)"', html)
    return m.group(1) if m else ''


print('Instalação do painel')
html, url = get('/admin/')
checar('acao=instalar' in url, 'sem usuários, /admin/ abre a instalação')
arquivo_chave = os.path.join(RAIZ, 'conteudo', 'chave-instalacao.txt')
checar(os.path.isfile(arquivo_chave), 'código de instalação gravado em conteudo/chave-instalacao.txt')
chave = open(arquivo_chave).read().strip()

senha_admin = secrets.token_urlsafe(12)
dados = {'nome': 'Teste admin', 'email': 'teste-admin@gastriclin.test', 'senha': senha_admin, 'senha2': senha_admin}
html, _ = post('/admin/?acao=instalar', {**dados, 'csrf': csrf(html), 'chave': 'ERRADO-0000-0000'})
checar('Código de instalação incorreto' in html, 'código errado é recusado')
html, url = get('/admin/?acao=instalar')
html, url = post('/admin/?acao=instalar', {**dados, 'csrf': csrf(html), 'chave': chave})
checar(url.rstrip('/').endswith('/admin') and 'Artigos do blog' in html, 'código certo cria o administrador e entra no painel')
checar(not os.path.exists(arquivo_chave), 'código de instalação é apagado depois do uso')
html, url = get('/admin/?acao=instalar')
checar('acao=entrar' in url or 'Artigos do blog' in html, 'instalação não pode ser repetida')

print('Administrador cria usuário da equipe de TI')
senha_editor = secrets.token_urlsafe(12)
html, _ = get('/admin/?acao=usuarios')
html, _ = post('/admin/?acao=usuarios', {'csrf': csrf(html), 'operacao': 'criar', 'nome': 'Teste editor', 'email': 'teste-editor@gastriclin.test', 'papel': 'editor', 'senha': senha_editor})
checar('teste-editor@gastriclin.test' in html, 'usuário da equipe de TI criado pelo painel')

with open(SAIDA, 'w') as f:
    f.write(f'SENHA_admin={senha_admin}\nSENHA_editor={senha_editor}\n')
print('\n' + ('TUDO OK' if not falhas else f'{len(falhas)} FALHA(S)'))
sys.exit(1 if falhas else 0)
