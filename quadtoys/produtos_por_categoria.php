<?php
require_once 'includes/config.php';

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: categorias.php');
    exit();
}
$categoria_id = (int) $_GET['id'];

// Buscar categoria
try {
    $stmt = $conn->prepare("SELECT * FROM categorias WHERE categoria_id = ?");
    $stmt->execute([$categoria_id]);
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$categoria) {
        header('Location: categorias.php');
        exit();
    }
} catch (PDOException $e) {
    die('Erro ao buscar categoria: ' . htmlspecialchars($e->getMessage()));
}

// Buscar produtos da categoria
try {
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE categoria_id = ? ORDER BY nome");
    $stmt->execute([$categoria_id]);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erro ao buscar produtos: ' . htmlspecialchars($e->getMessage()));
}

include 'includes/header.php';
?>

<div class="container">
    <div class="categoria-banner">
        <h1><?= htmlspecialchars($categoria['nome']) ?></h1>
        <p><?= htmlspecialchars($categoria['descricao'] ?? 'Explore nossa coleção de ' . $categoria['nome']) ?></p>
    </div>

    <div class="produtos-container">
        <?php if (count($produtos) > 0): ?>
            <div class="produtos-grid">
                <?php foreach ($produtos as $produto): ?>
                    <div class="product-card"
                         data-id="<?= $produto['produto_id'] ?>"
                         data-name="<?= htmlspecialchars($produto['nome']) ?>"
                         data-price="<?= $produto['preco'] ?>">
                        <div class="product-img">
                            <img src="images/produtos/produto_<?= $produto['produto_id'] ?>.jpg"
                                 onerror="this.onerror=null;this.src='images/placeholder.jpg';"
                                 alt="<?= htmlspecialchars($produto['nome']) ?>">
                            <?php if ($produto['estoque'] <= 0): ?>
                                <div class="product-badge" style="background-color:#7f8c8d;">ESGOTADO</div>
                            <?php elseif ($produto['estoque'] <= 5): ?>
                                <div class="product-badge">ÚLTIMAS</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-content">
                            <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                            <p class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                            <div style="display:flex; gap:8px; margin-top:10px;">
                                <a href="produto.php?id=<?= $produto['produto_id'] ?>" class="btn-secundario">Detalhes</a>
                                <?php if ($produto['estoque'] > 0): ?>
                                    <button class="btn add-to-cart" style="flex:1; margin:0;">+ Carrinho</button>
                                <?php else: ?>
                                    <button class="btn" disabled style="flex:1; margin:0; opacity:0.5; cursor:not-allowed;">Esgotado</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="sem-produtos">
                <h3>Nenhum produto encontrado nesta categoria</h3>
                <p>Estamos trabalhando para adicionar novos produtos em breve!</p>
                <a href="categorias.php">Ver outras categorias</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .categoria-banner {
        background: linear-gradient(135deg, #2c3e50 0%, #e74c3c 100%);
        color: white;
        padding: 40px 20px;
        text-align: center;
        margin: 30px 0;
        border-radius: 8px;
    }
    .categoria-banner h1 { font-size: 2.3em; margin-bottom: 12px; }
    .categoria-banner p { font-size: 1.05em; max-width: 800px; margin: 0 auto; opacity: 0.95; }
    .produtos-container { padding: 20px 0 50px; }
    .produtos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 25px;
    }
    .btn-secundario {
        background-color: #ecf0f1;
        color: #2c3e50;
        padding: 8px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        font-size: 0.9rem;
        text-align: center;
        transition: background-color 0.3s;
    }
    .btn-secundario:hover { background-color: #bdc3c7; }
    .sem-produtos {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 8px;
    }
    .sem-produtos h3 { margin-bottom: 15px; color: #2c3e50; }
    .sem-produtos p { margin-bottom: 25px; color: #666; }
    .sem-produtos a {
        display: inline-block;
        background-color: #4CAF50;
        color: white;
        padding: 12px 25px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
    }
    .sem-produtos a:hover { background-color: #45a049; }
</style>

<?php include 'includes/footer.php'; ?>
