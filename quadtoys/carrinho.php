<?php
require_once 'includes/config.php';

if (!isset($_SESSION['cliente_id'])) {
    header('Location: login.php?redirect=carrinho.php');
    exit();
}

// Atualizar quantidade
if (isset($_POST['atualizar']) && isset($_POST['carrinho_id']) && isset($_POST['quantidade'])) {
    $carrinho_id = (int) $_POST['carrinho_id'];
    $nova_qtd = (int) $_POST['quantidade'];
    if ($nova_qtd < 1) $nova_qtd = 1;

    try {
        $stmt = $conn->prepare("UPDATE carrinho_compras SET quantidade = ? WHERE carrinho_id = ? AND cliente_id = ?");
        $stmt->execute([$nova_qtd, $carrinho_id, $_SESSION['cliente_id']]);
    } catch (PDOException $e) {
        // erro silencioso
    }
    header('Location: carrinho.php');
    exit();
}

// Remover item
if (isset($_GET['remover'])) {
    $carrinho_id = (int) $_GET['remover'];
    try {
        $stmt = $conn->prepare("DELETE FROM carrinho_compras WHERE carrinho_id = ? AND cliente_id = ?");
        $stmt->execute([$carrinho_id, $_SESSION['cliente_id']]);
    } catch (PDOException $e) {
        // erro silencioso
    }
    header('Location: carrinho.php');
    exit();
}

// Buscar itens do carrinho — SEM coluna 'imagem' que não existe no schema
try {
    $stmt = $conn->prepare(
        "SELECT c.carrinho_id, c.quantidade, p.produto_id, p.nome, p.preco, p.estoque
         FROM carrinho_compras c
         JOIN produtos p ON c.produto_id = p.produto_id
         WHERE c.cliente_id = ?"
    );
    $stmt->execute([$_SESSION['cliente_id']]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erro: ' . htmlspecialchars($e->getMessage()));
}

$total = 0;
foreach ($itens as $item) {
    $total += $item['preco'] * $item['quantidade'];
}

include 'includes/header.php';
?>

<div class="container" style="padding: 30px 0;">
    <h1 style="color:#2c3e50; margin-bottom:25px;">Seu Carrinho</h1>

    <?php if (empty($itens)): ?>
        <div class="carrinho-vazio">
            <p>🛒</p>
            <h2>Seu carrinho está vazio</h2>
            <p>Que tal explorar nossos produtos?</p>
            <a href="colecoes.php" class="btn">Ver Coleções</a>
        </div>
    <?php else: ?>
        <div class="carrinho-wrapper">
            <table class="carrinho-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço Unit.</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <img src="images/produtos/produto_<?= $item['produto_id'] ?>.jpg"
                                         onerror="this.onerror=null;this.src='images/placeholder.jpg';"
                                         width="60" height="60"
                                         style="object-fit:cover; border-radius:4px;">
                                    <a href="produto.php?id=<?= $item['produto_id'] ?>" style="color:#2c3e50; text-decoration:none; font-weight:500;">
                                        <?= htmlspecialchars($item['nome']) ?>
                                    </a>
                                </div>
                            </td>
                            <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                            <td>
                                <form method="POST" style="display:flex; gap:5px; align-items:center;">
                                    <input type="hidden" name="atualizar" value="1">
                                    <input type="hidden" name="carrinho_id" value="<?= $item['carrinho_id'] ?>">
                                    <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>"
                                           min="1" max="<?= $item['estoque'] ?>"
                                           style="width:60px; padding:4px;">
                                    <button type="submit" class="btn-mini">↻</button>
                                </form>
                            </td>
                            <td><strong>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></strong></td>
                            <td>
                                <a href="carrinho.php?remover=<?= $item['carrinho_id'] ?>"
                                   class="btn-remover"
                                   onclick="return confirm('Remover este item?');">Remover</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="carrinho-total">
                <div class="total-box">
                    <span>Total:</span>
                    <strong>R$ <?= number_format($total, 2, ',', '.') ?></strong>
                </div>
                <div style="display:flex; gap:10px;">
                    <a href="colecoes.php" class="btn-secundario">Continuar comprando</a>
                    <a href="checkout.php" class="btn">Finalizar Compra</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .carrinho-vazio {
        text-align: center;
        background: white;
        padding: 60px 20px;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .carrinho-vazio p:first-child { font-size: 4em; margin-bottom: 10px; }
    .carrinho-vazio h2 { color: #2c3e50; margin-bottom: 10px; }
    .carrinho-vazio .btn { display: inline-block; margin-top: 20px; }

    .carrinho-wrapper {
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .carrinho-table {
        width: 100%;
        border-collapse: collapse;
    }
    .carrinho-table th {
        background: #ecf0f1;
        padding: 12px;
        text-align: left;
        color: #2c3e50;
        font-weight: 600;
    }
    .carrinho-table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    .btn-mini {
        background: #4CAF50;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
    }
    .btn-remover {
        color: #e74c3c;
        text-decoration: none;
        font-size: 0.9em;
    }
    .btn-remover:hover { text-decoration: underline; }

    .carrinho-total {
        margin-top: 25px;
        padding-top: 20px;
        border-top: 2px solid #ecf0f1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .total-box {
        font-size: 1.3em;
    }
    .total-box strong {
        color: #e74c3c;
        margin-left: 10px;
        font-size: 1.2em;
    }
    .btn-secundario {
        background-color: #ecf0f1;
        color: #2c3e50;
        padding: 10px 20px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
    }
    .btn-secundario:hover { background-color: #bdc3c7; }
    @media (max-width: 600px) {
        .carrinho-table { font-size: 0.85em; }
        .carrinho-table th, .carrinho-table td { padding: 8px 5px; }
    }
</style>

<?php include 'includes/footer.php'; ?>
