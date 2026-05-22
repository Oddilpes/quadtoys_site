<?php
require_once 'includes/config.php';

// Validar ID
$produto_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;
if ($produto_id <= 0) {
    header('Location: categorias.php');
    exit();
}

// Buscar produto + categoria
try {
    $stmt = $conn->prepare(
        "SELECT p.*, c.nome AS categoria_nome
         FROM produtos p
         LEFT JOIN categorias c ON p.categoria_id = c.categoria_id
         WHERE p.produto_id = ?"
    );
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erro ao buscar produto: ' . htmlspecialchars($e->getMessage()));
}

if (!$produto) {
    header('Location: categorias.php');
    exit();
}

// Avaliações aprovadas
try {
    $stmt = $conn->prepare(
        "SELECT a.*, cl.nome AS cliente_nome
         FROM avaliacoes a
         JOIN clientes cl ON a.cliente_id = cl.cliente_id
         WHERE a.produto_id = ? AND a.aprovado = 1
         ORDER BY a.data_avaliacao DESC"
    );
    $stmt->execute([$produto_id]);
    $avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $avaliacoes = [];
}

$mediaNota = 0;
if (count($avaliacoes) > 0) {
    $soma = array_sum(array_column($avaliacoes, 'nota'));
    $mediaNota = round($soma / count($avaliacoes), 1);
}

include 'includes/header.php';
?>

<div class="container" style="padding: 30px 0;">
    <a href="javascript:history.back()" style="display:inline-block; margin-bottom:20px; color:#4CAF50; text-decoration:none; font-weight:bold;">← Voltar</a>

    <div class="produto-detalhe">
        <div class="produto-imagem-grande">
            <img src="images/produtos/produto_<?= $produto['produto_id'] ?>.jpg"
                 onerror="this.onerror=null;this.src='images/placeholder.jpg';"
                 alt="<?= htmlspecialchars($produto['nome']) ?>">
        </div>

        <div class="produto-detalhe-info">
            <p class="produto-categoria-tag"><?= htmlspecialchars($produto['categoria_nome'] ?? 'Sem categoria') ?></p>
            <h1><?= htmlspecialchars($produto['nome']) ?></h1>

            <?php if (count($avaliacoes) > 0): ?>
                <div class="produto-avaliacao-resumo">
                    <span class="estrelas">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= round($mediaNota) ? '★' : '☆';
                        }
                        ?>
                    </span>
                    <span><?= $mediaNota ?>/5 (<?= count($avaliacoes) ?> avaliação<?= count($avaliacoes) > 1 ? 'ões' : '' ?>)</span>
                </div>
            <?php endif; ?>

            <p class="produto-preco-grande">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>

            <div class="produto-descricao">
                <h3>Descrição</h3>
                <p><?= nl2br(htmlspecialchars($produto['descricao'] ?? 'Sem descrição disponível.')) ?></p>
            </div>

            <div class="produto-especificacoes">
                <h3>Especificações</h3>
                <ul>
                    <?php if (!empty($produto['peso'])): ?>
                        <li><strong>Peso:</strong> <?= number_format($produto['peso'], 3, ',', '.') ?> kg</li>
                    <?php endif; ?>
                    <?php if (!empty($produto['dimensoes'])): ?>
                        <li><strong>Dimensões:</strong> <?= htmlspecialchars($produto['dimensoes']) ?></li>
                    <?php endif; ?>
                    <li>
                        <strong>Estoque:</strong>
                        <?php if ($produto['estoque'] > 0): ?>
                            <span style="color: #4CAF50;"><?= $produto['estoque'] ?> unidades disponíveis</span>
                        <?php else: ?>
                            <span style="color: #e74c3c;">Esgotado</span>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>

            <?php if ($produto['estoque'] > 0): ?>
                <div class="produto-card"
                     data-id="<?= $produto['produto_id'] ?>"
                     data-name="<?= htmlspecialchars($produto['nome']) ?>"
                     data-price="<?= $produto['preco'] ?>"
                     style="background:none; box-shadow:none; padding:0;">
                    <button class="add-to-cart btn" style="margin-top:20px; padding:15px 30px; font-size:1.1em;">
                        Adicionar ao Carrinho
                    </button>
                </div>
            <?php else: ?>
                <button class="btn" disabled style="margin-top:20px; padding:15px 30px; opacity:0.5; cursor:not-allowed;">
                    Produto Esgotado
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (count($avaliacoes) > 0): ?>
        <section class="avaliacoes-section">
            <h2>Avaliações dos Clientes</h2>
            <?php foreach ($avaliacoes as $av): ?>
                <div class="avaliacao-card">
                    <div class="avaliacao-header">
                        <strong><?= htmlspecialchars($av['cliente_nome']) ?></strong>
                        <span class="estrelas">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $av['nota'] ? '★' : '☆';
                            }
                            ?>
                        </span>
                        <span class="avaliacao-data"><?= date('d/m/Y', strtotime($av['data_avaliacao'])) ?></span>
                    </div>
                    <p><?= nl2br(htmlspecialchars($av['comentario'])) ?></p>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</div>

<style>
.produto-detalhe {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.produto-imagem-grande {
    height: 400px;
    background-color: #f5f5f5;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.produto-imagem-grande img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
.produto-categoria-tag {
    display: inline-block;
    background: #ecf0f1;
    color: #2c3e50;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85em;
    margin-bottom: 15px;
}
.produto-detalhe-info h1 {
    font-size: 2em;
    color: #2c3e50;
    margin-bottom: 15px;
}
.produto-avaliacao-resumo {
    margin-bottom: 20px;
    color: #666;
}
.estrelas {
    color: #f39c12;
    font-size: 1.2em;
    margin-right: 8px;
}
.produto-preco-grande {
    font-size: 2.2em;
    color: #e74c3c;
    font-weight: bold;
    margin-bottom: 25px;
}
.produto-descricao, .produto-especificacoes {
    margin-bottom: 20px;
}
.produto-descricao h3, .produto-especificacoes h3 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 1.1em;
}
.produto-especificacoes ul {
    list-style: none;
    padding: 0;
}
.produto-especificacoes li {
    padding: 6px 0;
    border-bottom: 1px solid #eee;
    color: #555;
}
.avaliacoes-section {
    margin-top: 40px;
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.avaliacoes-section h2 {
    color: #2c3e50;
    margin-bottom: 20px;
}
.avaliacao-card {
    border-bottom: 1px solid #eee;
    padding: 15px 0;
}
.avaliacao-card:last-child {
    border-bottom: none;
}
.avaliacao-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 8px;
}
.avaliacao-data {
    color: #999;
    font-size: 0.9em;
    margin-left: auto;
}
@media (max-width: 768px) {
    .produto-detalhe {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
