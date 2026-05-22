<?php
require_once 'includes/config.php';

// Buscar todas as categorias ativas
try {
    $stmt = $conn->query("SELECT * FROM categorias WHERE ativo = 1 ORDER BY nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erro ao buscar categorias: ' . htmlspecialchars($e->getMessage()));
}

// Buscar até 4 produtos por categoria
$categoriasComProdutos = [];
foreach ($categorias as $categoria) {
    try {
        $stmt = $conn->prepare("SELECT * FROM produtos WHERE categoria_id = ? AND estoque > 0 ORDER BY RAND() LIMIT 4");
        $stmt->execute([$categoria['categoria_id']]);
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($produtos) > 0) {
            $categoria['produtos'] = $produtos;
            $categoriasComProdutos[] = $categoria;
        }
    } catch (PDOException $e) {
        error_log("Erro ao buscar produtos da categoria {$categoria['nome']}: " . $e->getMessage());
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="banner-colecoes">
        <h1>Explore Nossas Coleções</h1>
        <p>Descubra um mundo de quadrinhos raros, action figures exclusivos e itens de colecionador que farão a alegria de qualquer fã.</p>
    </div>

    <div class="colecoes-container">
        <?php if (count($categoriasComProdutos) === 0): ?>
            <p style="text-align:center; padding:40px;">Nenhuma coleção disponível no momento.</p>
        <?php endif; ?>

        <?php foreach ($categoriasComProdutos as $categoria): ?>
            <div class="colecao-section">
                <div class="colecao-header">
                    <h2><?= htmlspecialchars($categoria['nome']) ?></h2>
                    <a href="produtos_por_categoria.php?id=<?= $categoria['categoria_id'] ?>">Ver todos →</a>
                </div>

                <div class="produtos-grid">
                    <?php foreach ($categoria['produtos'] as $produto): ?>
                        <div class="product-card"
                             data-id="<?= $produto['produto_id'] ?>"
                             data-name="<?= htmlspecialchars($produto['nome']) ?>"
                             data-price="<?= $produto['preco'] ?>">
                            <div class="product-img">
                                <img src="images/produtos/produto_<?= $produto['produto_id'] ?>.jpg"
                                     onerror="this.onerror=null;this.src='images/placeholder.jpg';"
                                     alt="<?= htmlspecialchars($produto['nome']) ?>">
                            </div>
                            <div class="product-content">
                                <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                                <p class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                                <div style="display:flex; gap:8px; margin-top:10px;">
                                    <a href="produto.php?id=<?= $produto['produto_id'] ?>" class="btn-secundario">Detalhes</a>
                                    <button class="btn add-to-cart" style="flex:1; margin:0;">+ Carrinho</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .banner-colecoes {
        padding: 50px 20px;
        text-align: center;
        margin: 30px 0;
        border-radius: 8px;
        background: linear-gradient(135deg, #2c3e50 0%, #4CAF50 100%);
        color: white;
    }
    .banner-colecoes h1 {
        font-size: 2.5em;
        margin-bottom: 15px;
    }
    .banner-colecoes p {
        font-size: 1.1em;
        max-width: 800px;
        margin: 0 auto;
        line-height: 1.6;
    }
    .colecoes-container { padding: 20px 0 50px; }
    .colecao-section { margin-bottom: 50px; }
    .colecao-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #ecf0f1;
        padding-bottom: 10px;
    }
    .colecao-header h2 { margin: 0; color: #2c3e50; font-size: 1.6em; }
    .colecao-header a {
        color: #4CAF50;
        text-decoration: none;
        font-weight: bold;
    }
    .produtos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
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
</style>

<?php include 'includes/footer.php'; ?>
