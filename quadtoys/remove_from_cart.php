<?php
require_once 'includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['cliente_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit();
}

$cart_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;

if ($cart_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM carrinho_compras WHERE carrinho_id = ? AND cliente_id = ?");
    $stmt->execute([$cart_id, $_SESSION['cliente_id']]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no banco']);
    error_log('remove_from_cart: ' . $e->getMessage());
}
