<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admins_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatórios</title>
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
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .report-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .report-card:hover {
            transform: translateY(-5px);
        }
        .report-card h3 {
            margin-top: 0;
            color: #4CAF50;
        }
        .report-card p {
            color: #666;
            margin-bottom: 20px;
        }
        .report-card a {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
        }
        .report-card a:hover {
            background-color: #45a049;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h2>Relatórios</h2>
    
    <div class="reports-grid">
        <div class="report-card">
            <h3>Relatório de Vendas</h3>
            <p>Visualize o desempenho de vendas por período, com gráficos e métricas detalhadas.</p>
            <a href="relatorio_vendas.php">Acessar</a>
        </div>
        
        <div class="report-card">
            <h3>Produtos Mais Vendidos</h3>
            <p>Veja quais produtos tiveram o melhor desempenho em um determinado período.</p>
            <a href="relatorio_vendas.php?tipo=produtos">Acessar</a>
        </div>
        
        <div class="report-card">
            <h3>Relatório por Categorias</h3>
            <p>Analise o desempenho de vendas agrupado por categorias de produtos.</p>
            <a href="relatorio_vendas.php?tipo=categoria">Acessar</a>
        </div>
        
        <div class="report-card">
            <h3>Relatório de Clientes</h3>
            <p>Veja estatísticas sobre os clientes cadastrados e seus padrões de compra.</p>
            <a href="relatorio_clientes.php">Acessar</a>
        </div>
    </div>
    
    <a href="admins_dashboard.php" class="btn-back">Voltar para o Dashboard</a>
</body>
</html>