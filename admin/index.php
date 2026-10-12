<?php
// Painel de administração do blog: /admin/
declare(strict_types=1);

define('PUBLICO', dirname(__DIR__));
require dirname(__DIR__) . '/app/admin/nucleo.php';

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');
iniciar_sessao();

$acao = $_GET['acao'] ?? 'artigos';
$post_req = $_SERVER['REQUEST_METHOD'] === 'POST';
if ($post_req) verificar_csrf();

// ---------------------------------------------------------------- Primeiro acesso: criar o administrador
// Só funciona enquanto não existe nenhum usuário, e exige o código gravado no servidor
// (conteudo/chave-instalacao.txt), que só quem tem acesso ao hPanel da Hostinger consegue ler.
if (!usuarios() && $acao !== 'instalar') redirecionar_admin('/admin/?acao=instalar');

if ($acao === 'instalar') {
    if (usuarios()) redirecionar_admin('/admin/?acao=entrar');
    $arquivo_chave = CONTEUDO . '/chave-instalacao.txt';
    if (!is_file($arquivo_chave)) {
        $chave = implode('-', str_split(strtoupper(bin2hex(random_bytes(6))), 4));
        if (!is_dir(CONTEUDO)) mkdir(CONTEUDO, 0755, true);
        file_put_contents($arquivo_chave, $chave . "\n", LOCK_EX);
    }
    $erro = '';
    if ($post_req) {
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $senha = (string) ($_POST['senha'] ?? '');
        if (login_bloqueado()) {
            $erro = 'Muitas tentativas. Aguarde ' . BLOQUEIO_MINUTOS . ' minutos e tente de novo.';
        } elseif (!hash_equals(trim((string) file_get_contents($arquivo_chave)), strtoupper(trim((string) ($_POST['chave'] ?? ''))))) {
            registrar_falha_login();
            $erro = 'Código de instalação incorreto.';
        } elseif ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = 'Informe nome e e-mail válidos.';
        } elseif (strlen($senha) < 10 || $senha !== ($_POST['senha2'] ?? '')) {
            $erro = 'A senha precisa ter pelo menos 10 caracteres e ser igual nos dois campos.';
        } else {
            salvar_usuarios([['nome' => $nome, 'email' => $email, 'papel' => 'admin', 'senha' => password_hash($senha, PASSWORD_DEFAULT), 'ativo' => true, 'criado_em' => date('Y-m-d')]]);
            @unlink($arquivo_chave);
            entrar($email, $senha);
            avisar('Administrador(a) criado(a). Bem-vindo(a) ao painel!');
            redirecionar_admin('/admin/');
        }
    }
    require APP . '/admin/telas/instalar.php';
    exit;
}

// ---------------------------------------------------------------- Entrar / sair
if ($acao === 'entrar') {
    $erro = '';
    if ($post_req) {
        if (login_bloqueado()) {
            $erro = 'Muitas tentativas. Aguarde ' . BLOQUEIO_MINUTOS . ' minutos e tente de novo.';
        } elseif (entrar(trim((string) ($_POST['email'] ?? '')), (string) ($_POST['senha'] ?? ''))) {
            redirecionar_admin('/admin/');
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
    require APP . '/admin/telas/entrar.php';
    exit;
}

if ($acao === 'sair' && $post_req) {
    sair();
    redirecionar_admin('/admin/?acao=entrar');
}

$usuario = exigir_login();

// ---------------------------------------------------------------- Artigos
if ($acao === 'artigos') {
    $filtro = $_GET['status'] ?? '';
    $busca = trim((string) ($_GET['busca'] ?? ''));
    $lista = posts(false);
    if (isset(STATUS_POST[$filtro])) $lista = array_filter($lista, fn($p) => $p['status'] === $filtro);
    if ($busca !== '') $lista = array_filter($lista, fn($p) => str_contains(normalizar($p['titulo']), normalizar($busca)));
    $contagem = array_count_values(array_column(posts(false), 'status'));
    require APP . '/admin/telas/artigos.php';
    exit;
}

if ($acao === 'editar') {
    $slug = (string) ($_GET['slug'] ?? '');
    $post = $slug !== '' ? post($slug, false) : null;
    if ($slug !== '' && !$post) {
        avisar('Artigo não encontrado.', 'erro');
        redirecionar_admin('/admin/');
    }
    $post ??= [
        'slug' => '', 'titulo' => '', 'titulo_seo' => '', 'descricao' => '', 'status' => 'rascunho',
        'publicado_em' => date('Y-m-d'), 'atualizado_em' => date('Y-m-d'), 'categorias' => [], 'palavras_chave' => [],
        'capa' => null, 'revisao_medica' => ['revisado' => false, 'por' => '', 'data' => ''], 'conteudo_html' => '',
    ];
    require APP . '/admin/telas/editar.php';
    exit;
}

if ($acao === 'salvar' && $post_req) {
    $original = (string) ($_POST['slug_original'] ?? '');
    $anterior = $original !== '' ? post($original, false) : null;
    $botao = (string) ($_POST['botao'] ?? 'rascunho');
    $volta = '/admin/?acao=editar' . ($original !== '' ? '&slug=' . rawurlencode($original) : '');

    try {
        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        if ($titulo === '') throw new RuntimeException('Informe o título do artigo.');
        $slug = gerar_slug(trim((string) ($_POST['slug'] ?? '')) ?: $titulo);
        if (!slug_valido($slug)) throw new RuntimeException('Endereço inválido. Use letras, números e hífens.');
        if ($anterior && $anterior['status'] === 'publicado' && $slug !== $original) {
            throw new RuntimeException('O endereço de um artigo já publicado não pode mudar: os links no Google parariam de funcionar.');
        }
        if ($slug !== $original && (post($slug, false) || servico($slug) || profissional($slug) || in_array($slug, ['blog', 'admin', 'sobre', 'clinico', 'convenios', 'exames', 'especialidades', 'bio', 'privacidade', 'assets', 'uploads'], true))) {
            throw new RuntimeException("Já existe uma página com o endereço /{$slug}/. Escolha outro.");
        }

        $descricao = trim((string) ($_POST['descricao'] ?? ''));
        if ($descricao === '') throw new RuntimeException('Escreva a descrição (resumo que aparece no Google).');
        $conteudo = html_seguro((string) ($_POST['conteudo'] ?? ''));
        if ($conteudo === '') throw new RuntimeException('O artigo está sem texto.');

        $lista_virgula = fn(string $campo) => array_values(array_filter(array_map('trim', explode(',', (string) ($_POST[$campo] ?? ''))), 'strlen'));
        $revisado = !empty($_POST['revisado']);
        $revisor = trim((string) ($_POST['revisor'] ?? ''));
        $data_revisao = (string) ($_POST['data_revisao'] ?? '');
        $data_pub = (string) ($_POST['publicado_em'] ?? '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_pub)) $data_pub = date('Y-m-d');

        $status = $botao === 'publicar' ? 'publicado' : 'rascunho';

        // A imagem só é processada depois de todas as validações (evita arquivos órfãos em uploads/)
        $capa = $anterior['capa'] ?? null;
        if (!empty($_POST['remover_capa'])) $capa = null;
        if (!empty($_FILES['capa']['name'])) $capa = processar_capa($_FILES['capa'], $slug);
        if ($capa) $capa['alt'] = trim((string) ($_POST['capa_alt'] ?? ''));

        $post = [
            'slug' => $slug,
            'titulo' => $titulo,
            'titulo_seo' => trim((string) ($_POST['titulo_seo'] ?? '')),
            'descricao' => $descricao,
            'status' => $status,
            'publicado_em' => $data_pub,
            'atualizado_em' => date('Y-m-d'),
            'categorias' => $lista_virgula('categorias'),
            'palavras_chave' => $lista_virgula('palavras_chave'),
            'capa' => $capa,
            'revisao_medica' => ['revisado' => $revisado, 'por' => $revisor, 'data' => $data_revisao],
            'conteudo_html' => $conteudo,
            'editado_por' => $usuario['nome'],
        ];
        salvar_post($post);
        if ($original !== '' && $original !== $slug) excluir_post($original);
        if ($status === 'publicado' || ($anterior['status'] ?? '') === 'publicado') limpar_cache_site();

        avisar($status === 'publicado' ? 'Artigo publicado no site.' : 'Rascunho salvo (não aparece no site).');
        redirecionar_admin('/admin/?acao=editar&slug=' . rawurlencode($slug));
    } catch (Throwable $e) {
        $_SESSION['formulario'] = $_POST;
        avisar($e instanceof RuntimeException ? $e->getMessage() : 'Erro ao salvar: ' . $e->getMessage(), 'erro');
        redirecionar_admin($volta);
    }
}

if ($acao === 'excluir' && $post_req) {
    $slug = (string) ($_POST['slug'] ?? '');
    $p = post($slug, false);
    if ($p) {
        excluir_post($slug);
        if ($p['status'] === 'publicado') limpar_cache_site();
        avisar('Artigo movido para a lixeira (conteudo/lixeira/).');
    }
    redirecionar_admin('/admin/');
}

if ($acao === 'previa') {
    $post = post((string) ($_GET['slug'] ?? ''), false);
    if (!$post) redirecionar_admin('/admin/');
    $_SERVER['REQUEST_URI'] = "/{$post['slug']}/";
    require APP . '/paginas/artigo.php';
    exit;
}

// ---------------------------------------------------------------- Conta e usuários
if ($acao === 'senha') {
    if ($post_req) {
        $atual = (string) ($_POST['senha_atual'] ?? '');
        $nova = (string) ($_POST['senha_nova'] ?? '');
        if (!password_verify($atual, $usuario['senha'])) avisar('Senha atual incorreta.', 'erro');
        elseif (strlen($nova) < 10) avisar('A nova senha precisa ter pelo menos 10 caracteres.', 'erro');
        else {
            $lista = usuarios();
            foreach ($lista as &$u) if ($u['email'] === $usuario['email']) $u['senha'] = password_hash($nova, PASSWORD_DEFAULT);
            unset($u);
            salvar_usuarios($lista);
            avisar('Senha alterada.');
        }
        redirecionar_admin('/admin/?acao=senha');
    }
    require APP . '/admin/telas/senha.php';
    exit;
}

if ($acao === 'usuarios') {
    if (!pode('usuarios', $usuario)) redirecionar_admin('/admin/');
    if ($post_req) {
        $lista = usuarios();
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $operacao = (string) ($_POST['operacao'] ?? '');
        if ($operacao === 'criar') {
            $nome = trim((string) ($_POST['nome'] ?? ''));
            $papel = (string) ($_POST['papel'] ?? 'editor');
            $senha = (string) ($_POST['senha'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $nome === '' || !isset(PAPEIS[$papel])) avisar('Preencha nome, e-mail válido e papel.', 'erro');
            elseif (strlen($senha) < 10) avisar('A senha inicial precisa ter pelo menos 10 caracteres.', 'erro');
            elseif (in_array($email, array_column($lista, 'email'), true)) avisar('Já existe um usuário com esse e-mail.', 'erro');
            else {
                $lista[] = ['nome' => $nome, 'email' => $email, 'papel' => $papel, 'senha' => password_hash($senha, PASSWORD_DEFAULT), 'ativo' => true, 'criado_em' => date('Y-m-d')];
                salvar_usuarios($lista);
                avisar("Usuário {$nome} criado. Peça para trocar a senha no primeiro acesso.");
            }
        } elseif ($operacao === 'alternar' && $email !== $usuario['email']) {
            foreach ($lista as &$u) if ($u['email'] === $email) $u['ativo'] = !($u['ativo'] ?? true);
            unset($u);
            salvar_usuarios($lista);
            avisar('Acesso atualizado.');
        }
        redirecionar_admin('/admin/?acao=usuarios');
    }
    require APP . '/admin/telas/usuarios.php';
    exit;
}

redirecionar_admin('/admin/');
