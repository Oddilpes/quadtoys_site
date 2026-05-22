<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admins_login.php");
    exit;
}

// Incluir arquivo de configuração
require_once __DIR__ . '/../includes/config.php';

// Verificar se o ID foi fornecido
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID do produto não fornecido!";
    exit;
}

try {
    // Primeiro verificar se o produto existe
    $stmt = $conn->prepare("SELECT produto_id FROM produtos WHERE produto_id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() == 0) {
        echo "Produto não encontrado!";
        exit;
    }
    
    // Verificar se o produto está em algum carrinho
    $stmt = $conn->prepare("SELECT COUNT(*) FROM carrinho_compras WHERE produto_id = ?");
    $stmt->execute([$id]);
    $em_carrinho = $stmt->fetchColumn();
    
    // Verificar se o produto está em algum pedido
    $stmt = $conn->prepare("SELECT COUNT(*) FROM itens_pedido WHERE produto_id = ?");
    $stmt->execute([$id]);
    $em_pedido = $stmt->fetchColumn();
    
    if ($em_carrinho > 0 || $em_pedido > 0) {
        // Produto está em uso, não excluir definitivamente
        $stmt = $conn->prepare("UPDATE produtos SET estoque = 0 WHERE produto_id = ?");
        $stmt->execute([$id]);
        header("Location: produto_listar.php?msg=inativo");
        exit;
    } else {
        // Produto não está em uso, pode ser excluído
        $stmt = $conn->prepare("DELETE FROM produtos WHERE produto_id = ?");
        $stmt->execute([$id]);
        header("Location: produto_listar.php?msg=excluido");
        exit;
    }
} catch(PDOException $e) {
    echo "Erro ao excluir produto: " . $e->getMessage();
    exit;
}
?>