# GastriClin – site (PHP)

Site da GastriClin – Centro de Medicina Digestiva (Fortaleza – CE) em **PHP + HTML + CSS + JavaScript puro**, sem banco de dados e sem build. Roda em qualquer plano da Hostinger com PHP 8.1 ou superior (recomendado: 8.3).

Inclui um **painel do blog** em `/admin/`, para a equipe de TI publicar os artigos entregues pelo marketing.

## Como funciona a publicação (automática)

```
você altera e envia (git push) ──► GitHub verifica o site inteiro ──► se passar ──► Hostinger publica
                                   (páginas, painel e segurança)       (webhook)
```

- O workflow `.github/workflows/verificar-e-publicar.yml` roda a cada envio para a branch `main`.
- Se alguma verificação falhar, **nada é publicado** e o GitHub mostra o erro na aba *Actions*.
- Os artigos publicados pelo painel ficam **só no servidor** (pasta `conteudo/`), fora do Git: um novo deploy **nunca apaga** o que a TI publicou.

## Estrutura

```
/                         ← raiz do repositório = public_html na Hostinger
├── index.php             roteador: recebe todas as URLs e entrega a página certa
├── .htaccess             URLs amigáveis, HTTPS, compressão, cache, segurança e bloqueio das pastas internas
├── admin/                painel do blog
├── assets/
│   ├── css/site.css      todo o visual (cores, fontes, componentes)
│   ├── css/fontes.css    fontes Inter e Fraunces
│   ├── js/site.js        menu, cliques de agendamento e cookies
│   ├── fonts/  img/      fontes e fotos otimizadas (WebP)
│   └── blog/             capas dos artigos iniciais
├── app/                  (bloqueada na web)
│   ├── config.php        ★ dados da clínica: endereço, WhatsApp, links, responsável técnica, GA4, redirecionamentos
│   ├── dados/            ★ servicos.php, medicos.php, convenios.php · imagens.json (gerado)
│   ├── paginas/          uma página por arquivo (inicio.php, servico.php, artigo.php...)
│   ├── componentes/      cabeçalho, rodapé, cards, botões de agendar...
│   ├── layout/           início e fim de toda página
│   └── admin/            lógica e telas do painel
├── dados-iniciais/blog/  os 34 artigos do site antigo (copiados para conteudo/ no primeiro acesso)
├── originais/            fotos originais (fonte para gerar as versões WebP)
├── ferramentas/          scripts de verificação e manutenção
│
├── conteudo/             criada no servidor, fora do Git: artigos, usuários, lixeira
└── uploads/              criada no servidor, fora do Git: capas enviadas pelo painel
```

★ = arquivos editados no dia a dia. As pastas `app/`, `conteudo/`, `dados-iniciais/`, `ferramentas/` e `originais/`, além de `.git`, são bloqueadas pelo `.htaccess`.

## Testar no computador

```bash
php -S 127.0.0.1:8080 -t . index.php
```

Abra http://127.0.0.1:8080. O painel fica em http://127.0.0.1:8080/admin/. Para verificar tudo antes de enviar:

```bash
python ferramentas/verificar_site.py
```

## Configurar a publicação automática na Hostinger (uma vez)

1. **Backup do WordPress atual:** hPanel → *Arquivos → Backups*. Baixe arquivos e banco de dados e **guarde o banco MySQL** até o site novo estar aprovado.
2. **PHP:** hPanel → *Avançado → Configuração do PHP* → **8.3**.
3. **Pasta vazia:** a Hostinger só instala o Git numa pasta vazia. Para testar antes, crie um subdomínio (ex.: `novo.gastriclin.com.br`). Para a troca definitiva, esvazie a `public_html` (depois do backup).
4. **Conectar o repositório:** hPanel → *Avançado → Git*.
   - **Repositório:** o endereço do GitHub. Se o repositório for **privado**, use o endereço SSH (`git@github.com:USUARIO/REPOSITORIO.git`), copie a **chave SSH** que a Hostinger mostra nessa tela e cadastre em GitHub → repositório → *Settings → Deploy keys → Add deploy key* (somente leitura).
   - **Branch:** `main`. **Diretório:** deixe vazio (instala na `public_html` do domínio ou subdomínio).
   - Clique em **Criar** e depois em **Deploy**.
5. **Deploy automático só depois das verificações:** na mesma tela, ative o *Deploy automático* e **copie a URL do webhook**. No GitHub, **não** cadastre essa URL em *Webhooks*. Cadastre em *Settings → Secrets and variables → Actions → New repository secret*, com o nome `HOSTINGER_WEBHOOK`. Assim, o GitHub só chama a Hostinger quando todas as verificações passam.
   - **Renovar o cache depois de publicar:** em *Settings → Secrets and variables → Actions → aba Variables → New repository variable*, crie `SITE_URL` com o endereço do site (ex.: `https://gastriclin.com.br`, sem barra no final). Depois de cada publicação, o GitHub visita o site e ele apaga o cache de páginas da Hostinger. Sem essa variável, o site ainda se renova sozinho, mas pode mostrar a versão anterior por até 1 hora.
6. **Primeiro acesso ao painel:** abra `https://SEU-DOMINIO/admin/`. O painel pede um **código de instalação**. No hPanel, abra o *Gerenciador de Arquivos* → `public_html/conteudo/chave-instalacao.txt`, copie o código e crie o administrador. O arquivo é apagado sozinho depois disso.
7. **Conferir:** páginas, painel e endereços antigos (ex.: `/dr-matheus-brito/` deve redirecionar). Depois, envie o sitemap `https://gastriclin.com.br/sitemap.xml` no Google Search Console.

## Cache das páginas (Hostinger)

O `.htaccess` pede ao servidor (LiteSpeed) para guardar cada página pronta por **1 hora**. Isso deixa o site mais rápido, mas faz uma publicação nova demorar a aparecer se o cache não for limpo.

- **Publicação pelo GitHub:** a primeira página gerada depois da publicação percebe que o código mudou (data e tamanho dos arquivos, registrados em `conteudo/.versao-do-site`) e apaga o cache. O passo *Renovar o cache das páginas* do GitHub Actions força isso logo depois de publicar (precisa da variável `SITE_URL`).
- **Artigo publicado pelo painel:** o painel apaga o cache na hora.
- **Ainda vendo a versão antiga?** No hPanel, abra *Avançado → Cache Manager → Limpar todo o cache*. Se o **CDN da Hostinger** estiver ativo, limpe também o cache do CDN. Por último, recarregue o navegador com Ctrl+F5.

## Agenda online (/agendar/)

A página `/agendar/` mostra a **agenda da Doctoralia dentro do site** (widget oficial). Todo botão "Agendar online" do site leva a ela.

- **Fluxo do paciente:** especialidade → serviço → convênio (ou "sem plano de saúde") → especialista → horário. Ao escolher o horário, a Doctoralia abre a confirmação em nova aba, e o site não recebe os dados do paciente.
- **Código do widget:** em `app/paginas/agendar.php` (o `<a ... data-zl-widget-facility="gastriclin" ...>`). É o código fornecido pela Doctoralia; o carregamento do script é feito pela página, depois do `load`.
- **Plano B:** se o script da Doctoralia falhar ou for bloqueado (12 s sem o calendário), aparecem os botões "Abrir agenda na Doctoralia" e "Agendar pelo WhatsApp".
- **Só funciona em HTTPS:** o redimensionamento automático do widget só é aceito de `https://www.doctoralia.com.br`. Em `http://localhost` o iframe pode ficar com altura fixa de 450 px. Isso é só do teste local.
- **Cookies:** o widget grava um cookie de terceiros da Doctoralia (descrito na política de privacidade).
- **Trocar o tipo de widget:** o `data-zlw-type` define o formato (a Doctoralia tem outros, como botão flutuante). Use o gerador no painel da Doctoralia e substitua a tag `<a>`.

## Painel do blog (/admin/)

| Papel | Pode |
|---|---|
| **Equipe de TI** | Criar, editar, publicar, tirar do site e excluir artigos |
| **Administrador(a)** | Tudo da equipe de TI + criar e bloquear usuários |

- **Fluxo:** o marketing entrega o texto → a TI cola no editor, envia a capa e preenche título e descrição para o Google → **Publicar no site**. Também dá para salvar como **Rascunho**, ver a **Prévia** e **Tirar do site**.
- **Revisão médica (opcional):** se um médico revisar o texto, informe nome e CRM. Isso aparece no artigo.
- **Capa:** JPG, PNG ou WebP a partir de 600 px. É convertida automaticamente para WebP em 4 tamanhos.
- **Segurança:** senhas criptografadas, bloqueio de 15 min após 5 senhas erradas, proteção contra formulários forjados, limpeza do HTML e imagens sempre redesenhadas.
- **Exclusão:** vai para `conteudo/lixeira/` (recuperável).

## Manutenção

| Quero... | Onde |
|---|---|
| Publicar ou editar artigo | Painel `/admin/` (não precisa de Git) |
| Mudar telefone, endereço, horário, GA4 | `app/config.php` → enviar para o GitHub |
| Alterar texto de consulta ou exame | `app/dados/servicos.php` |
| Incluir, editar ou desativar médico | `app/dados/medicos.php` (`'ativo' => false` tira do site) |
| Incluir convênio | logo em `originais/convenios/`, `python ferramentas/gerar_imagens.py`, linha em `app/dados/convenios.php` |
| Trocar foto | original em `originais/<grupo>/<nome>.jpg` + `python ferramentas/gerar_imagens.py` |
| Cores e espaçamentos | `assets/css/site.css` |
| Redirecionar URL antiga | `REDIRECIONAMENTOS` em `app/config.php` |
| Quantidade de artigos por página do blog (hoje 9) | `ARTIGOS_POR_PAGINA` em `app/paginas/blog.php` |

Toda alteração de código: `git add . && git commit -m "descrição" && git push`. O GitHub verifica e a Hostinger publica.

**Backup:** as pastas `conteudo/` e `uploads/` existem só no servidor. Elas entram nos backups automáticos da Hostinger, mas vale baixar uma cópia de vez em quando.

## Ferramentas (`ferramentas/`)

| Script | Para quê |
|---|---|
| `verificar_site.py` | Verifica todas as páginas: erros de PHP, palavras coladas, títulos, H1, JSON-LD, imagens, links, botões e bloqueio das pastas internas |
| `testar_instalacao.py` | Testa o primeiro acesso ao painel num servidor zerado |
| `testar_painel.py` | Testa o painel: rascunho, publicação, capa, exclusão, permissões e segurança |
| `gerar_imagens.py` | Gera as versões WebP das fotos e o `app/dados/imagens.json` |
| `criar_usuario.php` | Cria usuário ou redefine senha pela linha de comando (se houver acesso SSH) |

## Qualidade (Lighthouse 13.5, perfil celular, 12/10/2026)

Desempenho entre 97 e 99, acessibilidade 100, boas práticas 100 e SEO 100 nas páginas principais. Estabilidade do layout (CLS) = 0.
