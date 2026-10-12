<?php
// Cria (ou redefine a senha de) um usuário do painel /admin/.
// Uso: php v2/ferramentas/criar_usuario.php "Nome" email@exemplo.com admin|editor   (editor = equipe de TI)
// A senha é pedida no terminal (não fica no histórico de comandos).

declare(strict_types=1);

if (PHP_SAPI !== 'cli') exit('Use pela linha de comando.');
require dirname(__DIR__) . '/app/admin/nucleo.php';

[$_, $nome, $email, $papel] = array_pad($argv, 4, null);
if (!$nome || !filter_var($email, FILTER_VALIDATE_EMAIL) || !isset(PAPEIS[$papel])) {
    fwrite(STDERR, "Uso: php v2/ferramentas/criar_usuario.php \"Nome\" email@exemplo.com admin|editor\n");
    exit(1);
}

$senha = getenv('SENHA_PAINEL') ?: null; // opcional, para automação
if ($senha === null) {
    echo 'Senha (mínimo 10 caracteres): ';
    $senha = trim((string) fgets(STDIN));
}
if (strlen($senha) < 10) {
    fwrite(STDERR, "A senha precisa ter pelo menos 10 caracteres.\n");
    exit(1);
}

$email = strtolower($email);
$lista = usuarios();
$existente = false;
foreach ($lista as &$u) {
    if ($u['email'] === $email) {
        $u = array_merge($u, ['nome' => $nome, 'papel' => $papel, 'senha' => password_hash($senha, PASSWORD_DEFAULT), 'ativo' => true]);
        $existente = true;
    }
}
unset($u);
if (!$existente) {
    $lista[] = ['nome' => $nome, 'email' => $email, 'papel' => $papel, 'senha' => password_hash($senha, PASSWORD_DEFAULT), 'ativo' => true, 'criado_em' => date('Y-m-d')];
}
salvar_usuarios($lista);
echo ($existente ? 'Usuário atualizado: ' : 'Usuário criado: ') . "{$nome} <{$email}> (" . PAPEIS[$papel] . ")\n";
