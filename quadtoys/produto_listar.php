<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admins_login.php");
    exit;
}


// Incluir o arquivo de configuração com a conexão ao banco
require_once __DIR__ . '/../includes/config.php';

// Buscar produtos do banco de dados
try {
    $stmt = $conn->prepare("SELECT produto_id, nome, preco FROM produtos ORDER BY produto_id");
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao buscar produtos: " . $e->getMessage();
    $produtos = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Produtos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .btn-add {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .btn-add:hover {
            background-color: #45a049;
            text-decoration: none;
        }
        .actions a {
            margin-right: 10px;
        }
        .empty-message {
            padding: 20px;
            text-align: center;
            background: white;
            border-radius: 4px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <h2>Lista de Produtos</h2>
    <a href="produto_adicionar.php" class="btn-add">Adicionar Produto</a>
    
    <?php if (empty($produtos)): ?>
        <div class="empty-message">
            <p>Nenhum produto cadastrado. Clique em "Adicionar Produto" para começar.</p>
        </div>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Preço</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($produtos as $p): ?>
            <tr>
                <td><?= $p['produto_id'] ?></td>
                <td><?= htmlspecialchars($p['nome']) ?></td>
                <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                <td class="actions">
                    <a href="produto_editar.php?id=<?= $p['produto_id'] ?>">Editar</a> |
                    <a href="produto_excluir.php?id=<?= $p['produto_id'] ?>" onclick="return confirm('Confirma excluir?')">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    
    <p><a href="admins_dashboard.php">Voltar para o Dashboard</a></p>
</body>
</html>