<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin/admins_login.php");
    exit;
}
/**
 * gerar_imagens.php
 * Execute UMA VEZ: http://localhost/quadtoys/gerar_imagens.php
 * Gera imagens para todos os produtos na pasta images/produtos/
 */

// Lista de produtos (id => [nome, cor de fundo, cor do texto])
$produtos = [
    1  => ['Funko Pop Iron Man',              [220,  50,  50], [255, 255, 255]],
    2  => ['Card Charizard Raro',             [255, 140,  20], [255, 255, 255]],
    3  => ['Action Figure Spider-Man',        [ 30,  80, 200], [255, 255, 255]],
    4  => ['Miniatura Batmóvel 1989',         [ 30,  30,  30], [255, 215,   0]],
    5  => ['Mangá One Piece Vol.1',           [ 20, 120, 200], [255, 255, 255]],
    6  => ['Varinha Harry Potter',            [100,  40, 140], [255, 215,   0]],
    7  => ['Action Figure Darth Vader',       [ 15,  15,  15], [200,   0,   0]],
    8  => ['Card Magic Black Lotus',          [ 50,  20,  80], [180, 150, 255]],
    9  => ['Funko Pop Goku',                  [ 30, 150, 220], [255, 220,   0]],
    10 => ['HQ Batman: O Cavaleiro das Trevas',[ 20,  20,  20], [255, 215,   0]],
];

// Ícones por produto (emojis desenhados como texto centralizado)
$icones = [
    1  => '🦾', 2  => '🃏', 3  => '🕷️', 4  => '🚗',
    5  => '📖', 6  => '🪄', 7  => '⚔️', 8  => '🌸',
    9  => '🔥', 10 => '🦇',
];

$largura  = 400;
$altura   = 400;
$pasta    = __DIR__ . '/images/produtos/';

// Criar pasta se não existir
if (!is_dir($pasta)) {
    mkdir($pasta, 0755, true);
}

$criados = [];
$erros   = [];

foreach ($produtos as $id => [$nome, $bg, $fg]) {
    $arquivo = $pasta . "produto_{$id}.jpg";

    $img = imagecreatetruecolor($largura, $altura);

    // Cor de fundo
    $corBg    = imagecolorallocate($img, $bg[0], $bg[1], $bg[2]);
    $corTexto = imagecolorallocate($img, $fg[0], $fg[1], $fg[2]);
    $corAcento = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $corBg);

    // Gradiente sutil (faixa mais clara no topo)
    for ($y = 0; $y < $altura / 2; $y++) {
        $fator = 1 - ($y / ($altura / 2)) * 0.25;
        $r = min(255, (int)($bg[0] * $fator + 40));
        $g = min(255, (int)($bg[1] * $fator + 40));
        $b = min(255, (int)($bg[2] * $fator + 40));
        $c = imagecolorallocate($img, $r, $g, $b);
        imageline($img, 0, $y, $largura, $y, $c);
    }

    // Círculo decorativo central
    $corCirculo = imagecolorallocatealpha($img, 255, 255, 255, 110);
    imagefilledellipse($img, $largura / 2, $altura / 2 - 30, 200, 200, $corCirculo);

    // Borda arredondada (retângulo com borda)
    $corBorda = imagecolorallocate($img, $fg[0], $fg[1], $fg[2]);
    imagerectangle($img, 5, 5, $largura - 6, $altura - 6, $corBorda);
    imagerectangle($img, 8, 8, $largura - 9, $altura - 9, $corBorda);

    // ID do produto (canto superior esquerdo)
    imagestring($img, 2, 18, 18, "#$id", $corTexto);

    // Nome do produto (quebra de linha manual, fonte embutida)
    $palavras = explode(' ', $nome);
    $linhas   = [];
    $linha    = '';
    foreach ($palavras as $palavra) {
        $teste = $linha ? "$linha $palavra" : $palavra;
        // fonte 5 = ~9px por char; limite ~28 chars por linha em 400px
        if (strlen($teste) > 22 && $linha !== '') {
            $linhas[] = $linha;
            $linha    = $palavra;
        } else {
            $linha = $teste;
        }
    }
    if ($linha) $linhas[] = $linha;

    $fonteTamanho = 5; // fonte built-in maior
    $alturaFonte  = imagefontheight($fonteTamanho);
    $totalLinhas  = count($linhas);
    $inicioY      = $altura - ($totalLinhas * ($alturaFonte + 6)) - 40;

    foreach ($linhas as $i => $l) {
        $larguraTexto = imagefontwidth($fonteTamanho) * strlen($l);
        $x = (int)(($largura - $larguraTexto) / 2);
        $y = $inicioY + $i * ($alturaFonte + 6);
        // Sombra
        imagestring($img, $fonteTamanho, $x + 1, $y + 1,
            $l, imagecolorallocate($img, 0, 0, 0));
        imagestring($img, $fonteTamanho, $x, $y, $l, $corTexto);
    }

    // Tag "QuadToys" no rodapé
    $tagTexto    = 'QuadToys';
    $larguraTag  = imagefontwidth(2) * strlen($tagTexto);
    $xTag        = (int)(($largura - $larguraTag) / 2);
    imagestring($img, 2, $xTag, $altura - 22, $tagTexto, $corAcento);

    // Salvar como JPEG
    if (imagejpeg($img, $arquivo, 90)) {
        $criados[] = "produto_{$id}.jpg — $nome";
    } else {
        $erros[] = "Falha ao salvar produto_{$id}.jpg";
    }
    imagedestroy($img);
}

// Gerar placeholder.jpg
$pastaPrincipal = __DIR__ . '/images/';
if (!is_dir($pastaPrincipal)) {
    mkdir($pastaPrincipal, 0755, true);
}
$placeholder = $pastaPrincipal . 'placeholder.jpg';
$img = imagecreatetruecolor($largura, $altura);
$cinza  = imagecolorallocate($img, 200, 200, 200);
$cinzaE = imagecolorallocate($img, 150, 150, 150);
$branco = imagecolorallocate($img, 255, 255, 255);
imagefill($img, 0, 0, $cinza);
imagerectangle($img, 5, 5, $largura - 6, $altura - 6, $cinzaE);
$msg = 'Imagem nao disponivel';
$xMsg = (int)(($largura - imagefontwidth(4) * strlen($msg)) / 2);
imagestring($img, 4, $xMsg, $altura / 2 - 10, $msg, $cinzaE);
imagejpeg($img, $placeholder, 85);
imagedestroy($img);

?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Gerador de Imagens — QuadToys</title>
<style>
  body { font-family: Arial, sans-serif; background: #f0f4f8; padding: 30px; }
  h1   { color: #2c3e50; }
  .ok  { color: #27ae60; }
  .err { color: #e74c3c; }
  .box { background: white; padding: 25px; border-radius: 8px;
         box-shadow: 0 2px 8px rgba(0,0,0,.1); max-width: 600px; }
  .previews { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 20px; }
  .previews img { width: 100px; height: 100px; object-fit: cover;
                  border-radius: 6px; border: 2px solid #ddd; }
  a.btn { display:inline-block; margin-top:20px; background:#4CAF50;
          color:white; padding:12px 24px; border-radius:6px;
          text-decoration:none; font-weight:bold; }
  a.btn:hover { background:#45a049; }
</style>
</head>
<body>
<div class="box">
  <h1>✅ Imagens Geradas!</h1>

  <?php if ($erros): ?>
    <p class="err"><strong>Erros:</strong></p>
    <ul><?php foreach ($erros as $e) echo "<li class='err'>$e</li>"; ?></ul>
  <?php endif; ?>

  <p class="ok"><strong><?= count($criados) ?> imagens criadas com sucesso:</strong></p>
  <ul>
    <?php foreach ($criados as $c) echo "<li class='ok'>✓ $c</li>"; ?>
    <li class="ok">✓ placeholder.jpg</li>
  </ul>

  <div class="previews">
    <?php foreach (array_keys($produtos) as $id): ?>
      <img src="images/produtos/produto_<?= $id ?>.jpg"
           title="Produto <?= $id ?>"
           onerror="this.style.border='2px solid red'">
    <?php endforeach; ?>
  </div>

  <a class="btn" href="index.php">← Voltar ao site</a>
</div>
</body>
</html>
