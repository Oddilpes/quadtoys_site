<?php
require_once 'includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['cliente_id'])) {
    echo json_encode(['success' => false, 'items' => []]);
    exit();
}

try {
    $stmt = $conn->prepare(
        "SELECT c.carrinho_id, c.quantidade, c.produto_id, p.nome, p.preco
         FROM carrinho_compras c
         JOIN produtos p ON c.produto_id = p.produto_id
         WHERE c.cliente_id = ?"
    );
    $stmt->execute([$_SESSION['cliente_id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'items' => $items]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no banco', 'items' => []]);
    error_log('get_cart: ' . $e->getMessage());
}
