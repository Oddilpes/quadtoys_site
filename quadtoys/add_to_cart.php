<?php
ob_start();
require_once 'includes/config.php';
ob_end_clean();

$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($is_ajax) {
    header('Content-Type: application/json');

    if (!isset($_SESSION['cliente_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuário não logado', 'need_login' => true]);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['produto_id']) || !is_numeric($data['produto_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de produto inválido']);
        exit();
    }

    $produto_id = (int) $data['produto_id'];
    $quantidade = isset($data['quantidade']) && is_numeric($data['quantidade'])
                  ? (int) $data['quantidade'] : 1;
    if ($quantidade < 1) $quantidade = 1;

    try {
        // Verificar produto e estoque
        $stmt = $conn->prepare("SELECT produto_id, nome, preco, estoque FROM produtos WHERE produto_id = ?");
        $stmt->execute([$produto_id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
            exit();
        }
        if ($produto['estoque'] < $quantidade) {
            echo json_encode(['success' => false, 'message' => 'Estoque insuficiente']);
            exit();
        }

        // Já existe no carrinho?
        $stmt = $conn->prepare("SELECT * FROM carrinho_compras WHERE cliente_id = ? AND produto_id = ?");
        $stmt->execute([$_SESSION['cliente_id'], $produto_id]);

        if ($stmt->rowCount() > 0) {
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            $nova_qtd = $item['quantidade'] + $quantidade;
            if ($nova_qtd > $produto['estoque']) {
                $nova_qtd = $produto['estoque'];
            }
            $up = $conn->prepare("UPDATE carrinho_compras SET quantidade = ? WHERE cliente_id = ? AND produto_id = ?");
            $up->execute([$nova_qtd, $_SESSION['cliente_id'], $produto_id]);
        } else {
            $ins = $conn->prepare("INSERT INTO carrinho_compras (cliente_id, produto_id, quantidade, data_adicao) VALUES (?, ?, ?, NOW())");
            $ins->execute([$_SESSION['cliente_id'], $produto_id, $quantidade]);
        }

        echo json_encode(['success' => true, 'message' => 'Produto adicionado ao carrinho!']);
        exit();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro no banco de dados']);
        error_log('add_to_cart: ' . $e->getMessage());
        exit();
    }
}

// Fallback: formulário tradicional
if (!isset($_SESSION['cliente_id'])) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit();
}

$produto_id = isset($_POST['produto_id']) && is_numeric($_POST['produto_id']) ? (int) $_POST['produto_id'] : 0;
$quantidade = isset($_POST['quantidade']) && is_numeric($_POST['quantidade']) ? (int) $_POST['quantidade'] : 1;
$redirect = $_POST['redirect'] ?? ($_SERVER['HTTP_REFERER'] ?? 'index.php');

if ($produto_id <= 0 || $quantidade < 1) {
    header("Location: $redirect");
    exit();
}

try {
    $stmt = $conn->prepare("SELECT produto_id, estoque FROM produtos WHERE produto_id = ?");
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto || $produto['estoque'] < $quantidade) {
        header("Location: $redirect");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM carrinho_compras WHERE cliente_id = ? AND produto_id = ?");
    $stmt->execute([$_SESSION['cliente_id'], $produto_id]);

    if ($stmt->rowCount() > 0) {
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        $nova_qtd = min($item['quantidade'] + $quantidade, $produto['estoque']);
        $up = $conn->prepare("UPDATE carrinho_compras SET quantidade = ? WHERE cliente_id = ? AND produto_id = ?");
        $up->execute([$nova_qtd, $_SESSION['cliente_id'], $produto_id]);
    } else {
        $ins = $conn->prepare("INSERT INTO carrinho_compras (cliente_id, produto_id, quantidade, data_adicao) VALUES (?, ?, ?, NOW())");
        $ins->execute([$_SESSION['cliente_id'], $produto_id, $quantidade]);
    }

    header("Location: $redirect");
    exit();
} catch (PDOException $e) {
    error_log('add_to_cart traditional: ' . $e->getMessage());
    header("Location: $redirect");
    exit();
}
