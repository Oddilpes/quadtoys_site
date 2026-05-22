<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin/admins_login.php");
    exit;
}
/**
 * organizar_imagens.php
 * Execute UMA VEZ: http://localhost/quadtoys/organizar_imagens.php
 * Copia as imagens existentes para a pasta images/produtos/ com os nomes corretos.
 */

// Mapeamento: produto_id => nome do arquivo original (que está em images/)
$mapeamento = [
    1  => 'iron_man.jpg',
    2  => 'charizard.jpg',
    3  => 'spider_man.jpg',
    4  => 'batmovel.jpg',
    5  => 'one_piece.jpg',
    6  => 'varinha.jpg',
    7  => 'darth_vader.jpg',
    8  => 'magic.jpg',
    9  => 'goku.jpg',
    10 => 'batmancav.jpg',
];

$pastaOrigem  = __DIR__ . '/images/';
$pastaDestino = __DIR__ . '/images/produtos/';

// Criar pasta destino se não existir
if (!is_dir($pastaDestino)) {
    mkdir($pastaDestino, 0755, true);
}

$sucesso = [];
$erros   = [];

foreach ($mapeamento as $id => $arquivo) {
    $origem  = $pastaOrigem . $arquivo;
    $destino = $pastaDestino . "produto_{$id}.jpg";

    if (!file_exists($origem)) {
        $erros[] = "❌ Arquivo não encontrado: <strong>images/{$arquivo}</strong> (produto $id)";
        continue;
    }

    // Copiar (mantém o original intacto)
    if (copy($origem, $destino)) {
        $sucesso[] = [
            'id'      => $id,
            'origem'  => $arquivo,
            'destino' => "produto_{$id}.jpg",
        ];
    } else {
        $erros[] = "❌ Falha ao copiar {$arquivo} → produto_{$id}.jpg";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Organizar Imagens — QuadToys</title>
<style>
  body { font-family: Arial, sans-serif; background: #f0f4f8; padding: 30px; margin: 0; }
  .box {
    background: white; padding: 30px; border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,.1); max-width: 650px; margin: 0 auto;
  }
  h1 { color: #2c3e50; margin-bottom: 5px; }
  .subtitulo { color: #7f8c8d; margin-bottom: 25px; font-size: .95em; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  th { background: #2c3e50; color: white; padding: 10px 12px; text-align: left; }
  td { padding: 9px 12px; border-bottom: 1px solid #eee; font-size: .93em; }
  tr:last-child td { border-bottom: none; }
  .ok  { color: #27ae60; font-weight: bold; }
  .err { color: #e74c3c; margin: 8px 0; }
  .previews { display: flex; flex-wrap: wrap; gap: 10px; margin: 20px 0; }
  .previews figure { margin: 0; text-align: center; }
  .previews img {
    width: 90px; height: 90px; object-fit: cover;
    border-radius: 6px; border: 2px solid #ddd; display: block;
  }
  .previews figcaption { font-size: .75em; color: #666; margin-top: 4px; }
  a.btn {
    display: inline-block; background: #4CAF50; color: white;
    padding: 12px 26px; border-radius: 6px; text-decoration: none;
    font-weight: bold; margin-top: 10px;
  }
  a.btn:hover { background: #45a049; }
</style>
</head>
<body>
<div class="box">
  <h1>🖼️ Organizar Imagens</h1>
  <p class="subtitulo">Copiando imagens para <code>images/produtos/</code></p>

  <?php if ($erros): ?>
    <div style="background:#fee; border:1px solid #f5b7b1; border-radius:6px; padding:15px; margin-bottom:20px;">
      <strong>Atenção:</strong>
      <?php foreach ($erros as $e) echo "<p class='err'>$e</p>"; ?>
    </div>
  <?php endif; ?>

  <?php if ($sucesso): ?>
    <p class="ok">✅ <?= count($sucesso) ?> imagens copiadas com sucesso!</p>

    <table>
      <thead>
        <tr><th>#</th><th>Arquivo original</th><th>Salvo como</th></tr>
      </thead>
      <tbody>
        <?php foreach ($sucesso as $s): ?>
          <tr>
            <td><?= $s['id'] ?></td>
            <td><code><?= htmlspecialchars($s['origem']) ?></code></td>
            <td><code class="ok"><?= $s['destino'] ?></code></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <p><strong>Pré-visualização:</strong></p>
    <div class="previews">
      <?php foreach ($sucesso as $s): ?>
        <figure>
          <img src="images/produtos/<?= $s['destino'] ?>"
               onerror="this.style.border='2px solid red'"
               alt="produto <?= $s['id'] ?>">
          <figcaption>Produto <?= $s['id'] ?></figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <a class="btn" href="index.php">← Voltar ao site</a>
</div>
</body>
</html>
