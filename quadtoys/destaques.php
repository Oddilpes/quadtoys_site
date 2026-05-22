<?php
require_once 'includes/config.php';

// Buscar produtos em destaque (flag destaque ou aleatórios)
try {
    $stmt = $conn->query(
        "SELECT p.*, c.nome AS categoria_nome
         FROM produtos p
         JOIN categorias c ON p.categoria_id = c.categoria_id
         WHERE p.estoque > 0 AND p.destaque = 1
         ORDER BY p.data_cadastro DESC
         LIMIT 12"
    );
    $destaques = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Se não tem o suficiente, completa com produtos aleatórios
    if (count($destaques) < 4) {
        $stmt = $conn->query(
            "SELECT p.*, c.nome AS categoria_nome
             FROM produtos p
             JOIN categorias c ON p.categoria_id = c.categoria_id
             WHERE p.estoque > 0
             ORDER BY RAND()
             LIMIT 12"
        );
        $destaques = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die('Erro ao buscar produtos em destaque: ' . htmlspecialchars($e->getMessage()));
}

include 'includes/header.php';
?>

<div class="container">
    <div class="destaques-container">
        <div class="page-header">
            <h1>Produtos em Destaque</h1>
            <p>Confira nossa seleção de produtos mais populares e exclusivos. Atualizamos esta lista regularmente com base nas tendências e preferências dos colecionadores.</p>
        </div>

        <?php if (count($destaques) > 0): ?>
            <div class="produtos-grid">
                <?php foreach ($destaques as $produto): ?>
                    <div class="product-card"
                         data-id="<?= $produto['produto_id'] ?>"
                         data-name="<?= htmlspecialchars($produto['nome']) ?>"
                         data-price="<?= $produto['preco'] ?>">
                        <div class="product-img" style="position:relative;">
                            <span class="destaque-badge">Destaque</span>
                            <img src="images/produtos/produto_<?= $produto['produto_id'] ?>.jpg"
                                 onerror="this.onerror=null;this.src='images/placeholder.jpg';"
                                 alt="<?= htmlspecialchars($produto['nome']) ?>">
                        </div>
                        <div class="product-content">
                            <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                            <p style="color:#777; font-size:0.85em; margin-bottom:8px;">Categoria: <?= htmlspecialchars($produto['categoria_nome']) ?></p>
                            <p class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                            <div style="display:flex; gap:8px; margin-top:10px;">
                                <a href="produto.php?id=<?= $produto['produto_id'] ?>" class="btn-secundario">Detalhes</a>
                                <button class="btn add-to-cart" style="flex:1; margin:0;">+ Carrinho</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align:center; padding:40px;">Nenhum produto em destaque no momento.</p>
        <?php endif; ?>
    </div>
</div>

<style>
    .destaques-container { padding: 30px 0; }
    .page-header { text-align: center; margin-bottom: 40px; }
    .page-header h1 { font-size: 2.5em; color: #2c3e50; margin-bottom: 15px; }
    .page-header p { color: #666; font-size: 1.05em; max-width: 700px; margin: 0 auto; line-height: 1.6; }
    .produtos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 25px;
    }
    .destaque-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #f39c12;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75em;
        font-weight: bold;
        z-index: 2;
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
