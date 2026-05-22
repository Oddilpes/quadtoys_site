<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admins_login.php");
    exit;
}

// Incluir arquivo de configuração
require_once __DIR__ . '/../includes/config.php';

// Buscar estatísticas para o dashboard
try {
    // Total de produtos
    $stmt = $conn->query("SELECT COUNT(*) FROM produtos");
    $total_produtos = $stmt->fetchColumn();
    
    // Total de pedidos
    $stmt = $conn->query("SELECT COUNT(*) FROM pedidos");
    $total_pedidos = $stmt->fetchColumn();
    
    // Total de clientes
    $stmt = $conn->query("SELECT COUNT(*) FROM clientes");
    $total_clientes = $stmt->fetchColumn();
    
    // Pedidos recentes
    $stmt = $conn->query("SELECT p.pedido_id, p.data_pedido, p.valor_total, c.nome 
                          FROM pedidos p 
                          JOIN clientes c ON p.cliente_id = c.cliente_id 
                          ORDER BY p.data_pedido DESC 
                          LIMIT 5");
    $pedidos_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $erro = "Erro ao buscar estatísticas: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrativo</title>
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
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card h3 {
            margin-top: 0;
            color: #555;
        }
        .card .number {
            font-size: 2.5rem;
            color: #4CAF50;
            font-weight: bold;
        }
        .menu {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .menu a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .menu a:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
        .logout {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 12px;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .logout:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <h2>Dashboard Administrativo</h2>
    
    <div class="menu">
        <a href="produto_listar.php">Gerenciar Produtos</a>
        <a href="pedido_listar.php">Gerenciar Pedidos</a>
        <a href="cliente_listar.php">Gerenciar Clientes</a>
        <a href="relatorios.php">Relatórios</a>
    </div>
    
    <div class="dashboard">
        <div class="card">
            <h3>Total de Produtos</h3>
            <div class="number"><?= $total_produtos ?? 0 ?></div>
        </div>
        
        <div class="card">
            <h3>Total de Pedidos</h3>
            <div class="number"><?= $total_pedidos ?? 0 ?></div>
        </div>
        
        <div class="card">
            <h3>Clientes Cadastrados</h3>
            <div class="number"><?= $total_clientes ?? 0 ?></div>
        </div>
    </div>
    
    <h3>Pedidos Recentes</h3>
    <?php if (!empty($pedidos_recentes)): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Total</th>
            </tr>
            <?php foreach ($pedidos_recentes as $pedido): ?>
            <tr>
                <td><?= $pedido['pedido_id'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></td>
                <td><?= htmlspecialchars($pedido['nome']) ?></td>
                <td>R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum pedido realizado ainda.</p>
    <?php endif; ?>
    
    <a href="admins_logout.php" class="logout">Sair</a>
</body>
</html>