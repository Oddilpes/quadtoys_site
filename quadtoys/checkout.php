<?php
require_once 'includes/config.php';

if (!isset($_SESSION['cliente_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

// Buscar itens do carrinho
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

if (empty($itens)) {
    header('Location: carrinho.php');
    exit();
}

$subtotal = 0;
foreach ($itens as $item) {
    $subtotal += $item['preco'] * $item['quantidade'];
}
$frete = $subtotal >= 200 ? 0 : 19.90;
$total = $subtotal + $frete;

$pedido_criado = false;
$pedido_id = null;
$erro = '';

// Processar finalização do pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {
    $metodo_pagamento = $_POST['metodo_pagamento'] ?? '';
    $cep = trim($_POST['cep'] ?? '');
    $logradouro = trim($_POST['logradouro'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $estado = trim($_POST['estado'] ?? '');

    if (empty($metodo_pagamento) || empty($cep) || empty($logradouro)
        || empty($numero) || empty($bairro) || empty($cidade) || empty($estado)) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } else {
        try {
            $conn->beginTransaction();

            // 1) Criar endereço de entrega
            $stmt = $conn->prepare(
                "INSERT INTO enderecos (cliente_id, tipo, cep, logradouro, numero, complemento, bairro, cidade, estado, padrao)
                 VALUES (?, 'entrega', ?, ?, ?, ?, ?, ?, ?, 0)"
            );
            $stmt->execute([
                $_SESSION['cliente_id'], $cep, $logradouro, $numero,
                $_POST['complemento'] ?? '', $bairro, $cidade, $estado
            ]);
            $endereco_id = $conn->lastInsertId();

            // 2) Criar pedido
            $stmt = $conn->prepare(
                "INSERT INTO pedidos (cliente_id, data_pedido, status_pedido, endereco_entrega_id,
                                       valor_produtos, valor_frete, valor_desconto, valor_total, metodo_pagamento)
                 VALUES (?, NOW(), 'pendente', ?, ?, ?, 0, ?, ?)"
            );
            $stmt->execute([$_SESSION['cliente_id'], $endereco_id, $subtotal, $frete, $total, $metodo_pagamento]);
            $pedido_id = $conn->lastInsertId();

            // 3) Criar itens do pedido e dar baixa no estoque
            $stmtItem = $conn->prepare(
                "INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario, subtotal)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmtEstoque = $conn->prepare("UPDATE produtos SET estoque = estoque - ? WHERE produto_id = ?");

            foreach ($itens as $item) {
                $sub = $item['preco'] * $item['quantidade'];
                $stmtItem->execute([
                    $pedido_id, $item['produto_id'], $item['quantidade'],
                    $item['preco'], $sub
                ]);
                $stmtEstoque->execute([$item['quantidade'], $item['produto_id']]);
            }

            // 4) Limpar carrinho
            $stmt = $conn->prepare("DELETE FROM carrinho_compras WHERE cliente_id = ?");
            $stmt->execute([$_SESSION['cliente_id']]);

            $conn->commit();
            $pedido_criado = true;
        } catch (PDOException $e) {
            $conn->rollBack();
            $erro = 'Erro ao processar pedido: ' . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container" style="padding: 30px 0;">
    <?php if ($pedido_criado): ?>
        <div class="pedido-sucesso">
            <div class="check-icon">✓</div>
            <h1>Pedido realizado com sucesso!</h1>
            <p>Seu pedido <strong>#<?= $pedido_id ?></strong> foi recebido e está em processamento.</p>
            <p>Você receberá uma confirmação por e-mail em breve.</p>
            <a href="index.php" class="btn">Voltar à Página Inicial</a>
        </div>
    <?php else: ?>
        <h1 style="color:#2c3e50; margin-bottom:25px;">Finalizar Compra</h1>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST" class="checkout-grid">
            <input type="hidden" name="finalizar" value="1">

            <div class="checkout-form-area">
                <h2>Endereço de Entrega</h2>
                <div class="form-row">
                    <div class="form-group" style="flex:1;">
                        <label>CEP *</label>
                        <input type="text" name="cep" required class="form-control" placeholder="00000-000">
                    </div>
                    <div class="form-group" style="flex:2;">
                        <label>Logradouro *</label>
                        <input type="text" name="logradouro" required class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group" style="flex:1;">
                        <label>Número *</label>
                        <input type="text" name="numero" required class="form-control">
                    </div>
                    <div class="form-group" style="flex:2;">
                        <label>Complemento</label>
                        <input type="text" name="complemento" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group" style="flex:2;">
                        <label>Bairro *</label>
                        <input type="text" name="bairro" required class="form-control">
                    </div>
                    <div class="form-group" style="flex:2;">
                        <label>Cidade *</label>
                        <input type="text" name="cidade" required class="form-control">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Estado *</label>
                        <input type="text" name="estado" maxlength="2" required class="form-control" placeholder="SP">
                    </div>
                </div>

                <h2 style="margin-top:30px;">Método de Pagamento</h2>
                <div class="pagamento-opcoes">
                    <label><input type="radio" name="metodo_pagamento" value="cartão de crédito" checked> 💳 Cartão de Crédito</label>
                    <label><input type="radio" name="metodo_pagamento" value="pix"> 💸 PIX</label>
                    <label><input type="radio" name="metodo_pagamento" value="boleto"> 📄 Boleto Bancário</label>
                </div>
            </div>

            <div class="checkout-resumo">
                <h2>Resumo do Pedido</h2>
                <?php foreach ($itens as $item): ?>
                    <div class="resumo-item">
                        <span><?= htmlspecialchars($item['nome']) ?> ×<?= $item['quantidade'] ?></span>
                        <span>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></span>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div class="resumo-item"><span>Subtotal:</span><span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span></div>
                <div class="resumo-item">
                    <span>Frete:</span>
                    <span><?= $frete == 0 ? '<strong style="color:#4CAF50;">GRÁTIS</strong>' : 'R$ ' . number_format($frete, 2, ',', '.') ?></span>
                </div>
                <hr>
                <div class="resumo-item total"><span>Total:</span><span>R$ <?= number_format($total, 2, ',', '.') ?></span></div>

                <button type="submit" class="btn" style="width:100%; margin-top:20px; padding:15px; font-size:1.1em;">
                    Confirmar Pedido
                </button>
                <a href="carrinho.php" style="display:block; text-align:center; margin-top:10px; color:#7f8c8d;">← Voltar ao carrinho</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<style>
    .pedido-sucesso {
        text-align: center;
        background: white;
        padding: 60px 30px;
        border-radius: 8px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        max-width: 600px;
        margin: 0 auto;
    }
    .check-icon {
        width: 80px;
        height: 80px;
        background: #4CAF50;
        color: white;
        font-size: 3em;
        line-height: 80px;
        border-radius: 50%;
        margin: 0 auto 20px;
    }
    .pedido-sucesso h1 { color: #2c3e50; margin-bottom: 15px; }
    .pedido-sucesso p { color: #555; margin-bottom: 10px; }
    .pedido-sucesso .btn { display: inline-block; margin-top: 25px; }

    .checkout-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
    }
    .checkout-form-area, .checkout-resumo {
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .checkout-form-area h2, .checkout-resumo h2 {
        color: #2c3e50;
        margin-bottom: 18px;
        font-size: 1.3em;
    }
    .form-row { display: flex; gap: 12px; }
    .form-group { margin-bottom: 15px; }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #555;
        font-size: 0.9em;
    }
    .form-control {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.95em;
    }
    .form-control:focus { outline: none; border-color: #4CAF50; }

    .pagamento-opcoes {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .pagamento-opcoes label {
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .pagamento-opcoes label:hover { border-color: #4CAF50; background: #f9f9f9; }

    .resumo-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 0.95em;
        color: #555;
    }
    .resumo-item.total {
        font-size: 1.2em;
        font-weight: bold;
        color: #2c3e50;
    }
    .resumo-item.total span:last-child { color: #e74c3c; }
    .checkout-resumo hr { border: none; border-top: 1px solid #eee; margin: 10px 0; }

    .alert {
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .alert-danger { background: #fee; color: #c0392b; border: 1px solid #f5b7b1; }

    @media (max-width: 768px) {
        .checkout-grid { grid-template-columns: 1fr; }
        .form-row { flex-direction: column; gap: 0; }
    }
</style>

<?php include 'includes/footer.php'; ?>
