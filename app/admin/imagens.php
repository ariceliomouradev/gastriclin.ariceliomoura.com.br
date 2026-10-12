<?php
// Capas do blog enviadas pelo painel: valida, converte para WebP e gera os tamanhos usados no site.
// A imagem é sempre redesenhada pelo GD, o que descarta qualquer conteúdo escondido no arquivo original.

declare(strict_types=1);

const LARGURAS_CAPA = [360, 540, 720, 1280];
const TAMANHO_MAXIMO_UPLOAD = 8 * 1024 * 1024; // 8 MB

/**
 * Processa $_FILES['capa'] e devolve ['arquivo' => 'uploads/blog/<slug>', 'w', 'h', 'larguras'].
 * Lança RuntimeException com mensagem amigável em caso de problema.
 */
function processar_capa(array $arquivo, string $slug): array
{
    if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException(match ($arquivo['error'] ?? 0) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'A imagem é grande demais. Envie um arquivo de até 8 MB.',
            default => 'Não foi possível receber a imagem. Tente novamente.',
        });
    }
    if ($arquivo['size'] > TAMANHO_MAXIMO_UPLOAD) throw new RuntimeException('A imagem é grande demais. Envie um arquivo de até 8 MB.');
    if (!is_uploaded_file($arquivo['tmp_name'])) throw new RuntimeException('Envio de imagem inválido.');

    $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
    $abrir = [
        'image/jpeg' => 'imagecreatefromjpeg',
        'image/png' => 'imagecreatefrompng',
        'image/webp' => 'imagecreatefromwebp',
    ][$tipo] ?? null;
    if (!$abrir) throw new RuntimeException('Formato não aceito. Envie JPG, PNG ou WebP.');

    $info = @getimagesize($arquivo['tmp_name']);
    if (!$info || $info[0] < 600) throw new RuntimeException('A imagem precisa ter pelo menos 600 pixels de largura.');
    if ($info[0] * $info[1] > 40_000_000) throw new RuntimeException('A imagem tem resolução alta demais (máximo 40 megapixels).');

    $original = @$abrir($arquivo['tmp_name']);
    if (!$original) throw new RuntimeException('Não foi possível ler a imagem.');

    // Fotos de celular: corrige a rotação
    if ($tipo === 'image/jpeg' && function_exists('exif_read_data')) {
        $orientacao = @exif_read_data($arquivo['tmp_name'])['Orientation'] ?? 1;
        $graus = [3 => 180, 6 => -90, 8 => 90][$orientacao] ?? 0;
        if ($graus) $original = imagerotate($original, $graus, 0);
    }

    $w = imagesx($original);
    $h = imagesy($original);
    $pasta = PUBLICO . '/uploads/blog';
    if (!is_dir($pasta)) mkdir($pasta, 0755, true);

    // Versão nova recebe sufixo de data para o navegador não mostrar a capa antiga do cache
    $base = $slug . '-' . date('YmdHis');
    $feitas = [];
    foreach (LARGURAS_CAPA as $lw) {
        $lw = min($lw, $w);
        if (in_array($lw, $feitas, true)) continue;
        $lh = (int) round($h * $lw / $w);
        $nova = imagecreatetruecolor($lw, $lh);
        imagefill($nova, 0, 0, imagecolorallocate($nova, 255, 255, 255)); // fundo branco para PNG transparente
        imagecopyresampled($nova, $original, 0, 0, 0, 0, $lw, $lh, $w, $h);
        if (!imagewebp($nova, "{$pasta}/{$base}-{$lw}.webp", 80)) throw new RuntimeException('Não foi possível salvar a imagem. Verifique a permissão da pasta uploads/.');
        imagedestroy($nova);
        $feitas[] = $lw;
    }
    imagedestroy($original);

    return ['arquivo' => "uploads/blog/{$base}", 'w' => $w, 'h' => $h, 'larguras' => $feitas];
}
