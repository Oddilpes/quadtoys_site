<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admins_login.php");
    exit;
}

// Incluir arquivo de configuração
require_once __DIR__ . '/../includes/config.php';

// Período para o relatório (padrão é o mês atual)
$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-t');

// Tipo de relatório
$tipo_relatorio = $_GET['tipo'] ?? 'diario';

try {
    // Inicializar dados
    $labels = [];
    $dados_vendas = [];
    $total_periodo = 0;
    $media_periodo = 0;
    $total_pedidos = 0;
    $ticket_medio = 0;
    
    // Relatório diário (dentro do período selecionado)
    if ($tipo_relatorio == 'diario') {
        $sql = "SELECT DATE(data_pedido) as data, 
                       COUNT(*) as total_pedidos, 
                       SUM(valor_total) as total_vendas 
                FROM pedidos 
                WHERE data_pedido BETWEEN ? AND ? 
                  AND status != 'cancelado'
                GROUP BY DATE(data_pedido) 
                ORDER BY data";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute([$data_inicio, $data_fim]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($resultados as $row) {
            $labels[] = date('d/m', strtotime($row['data']));
            $dados_vendas[] = floatval($row['total_vendas']);
            $total_periodo += floatval($row['total_vendas']);
            $total_pedidos += intval($row['total_pedidos']);
        }
    } 
    // Relatório mensal (últimos 12 meses)
    elseif ($tipo_relatorio == 'mensal') {
        // Ajustar data fim para fim do mês atual se não for final de mês
        if (date('Y-m-d') != date('Y-m-t')) {
            $data_fim = date('Y-m-t');
        }
        
        // Data início de 12 meses atrás
        $data_inicio_12m = date('Y-m-01', strtotime('-11 months'));
        
        $sql = "SELECT DATE_FORMAT(data_pedido, '%Y-%m') as mes, 
                       COUNT(*) as total_pedidos, 
                       SUM(valor_total) as total_vendas 
                FROM pedidos 
                WHERE data_pedido BETWEEN ? AND ? 
                  AND status != 'cancelado'
                GROUP BY DATE_FORMAT(data_pedido, '%Y-%m') 
                ORDER BY mes";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute([$data_inicio_12m, $data_fim]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($resultados as $row) {
            $ano_mes = explode('-', $row['mes']);
            $labels[] = date('M/y', mktime(0, 0, 0, $ano_mes[1], 1, $ano_mes[0]));
            $dados_vendas[] = floatval($row['total_vendas']);
            $total_periodo += floatval($row['total_vendas']);
            $total_pedidos += intval($row['total_pedidos']);
        }
    }
    // Relatório por categoria
    elseif ($tipo_relatorio == 'categoria') {
        $sql = "SELECT c.nome as categoria, 
                       COUNT(*) as total_pedidos, 
                       SUM(i.subtotal) as total_vendas 
                FROM itens_pedido i
                JOIN produtos p ON i.produto_id = p.produto_id
                JOIN categorias c ON p.categoria_id = c.categoria_id
                JOIN pedidos pd ON i.pedido_id = pd.pedido_id
                WHERE pd.data_pedido BETWEEN ? AND ? 
                  AND pd.status != 'cancelado'
                GROUP BY c.categoria_id 
                ORDER BY total_vendas DESC";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute([$data_inicio, $data_fim]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($resultados as $row) {
            $labels[] = $row['categoria'];
            $dados_vendas[] = floatval($row['total_vendas']);
            $total_periodo += floatval($row['total_vendas']);
            $total_pedidos += intval($row['total_pedidos']);
        }
    }
    
    // Calcular métricas adicionais
    if (count($dados_vendas) > 0) {
        $media_periodo = $total_periodo / count($dados_vendas);
    }
    
    if ($total_pedidos > 0) {
        $ticket_medio = $total_periodo / $total_pedidos;
    }
    
    // Produtos mais vendidos
    $sql_produtos = "SELECT p.nome, COUNT(*) as total_vendas, SUM(i.subtotal) as valor_total
                     FROM itens_pedido i
                     JOIN produtos p ON i.produto_id = p.produto_id
                     JOIN pedidos pd ON i.pedido_id = pd.pedido_id
                     WHERE pd.data_pedido BETWEEN ? AND ? 
                       AND pd.status != 'cancelado'
                     GROUP BY p.produto_id
                     ORDER BY total_vendas DESC
                     LIMIT 5";
                     
    $stmt_produtos = $conn->prepare($sql_produtos);
    $stmt_produtos->execute([$data_inicio, $data_fim]);
    $produtos_mais_vendidos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    echo "Erro ao gerar relatório: " . $e->getMessage();
    exit;
}

// Converter dados para JSON para uso no gráfico
$json_labels = json_encode($labels);
$json_dados = json_encode($dados_vendas);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório de Vendas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h2, h3 {
            color: #333;
        }
        .filters {
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .filters form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
        }
        .filters label {
            display: block;
            margin-bottom: 5px;
        }
        .filters select, .filters input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filters button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .filters button:hover {
            background-color: #45a049;
        }
        .report-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .metrics {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .metric-card {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .metric-title {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }
        .metric-value {
            font-size: 1.8em;
            font-weight: bold;
            color: #4CAF50;
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
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            color: #4CAF50;
            text-decoration: none;
        }
        @media (max-width: 768px) {
            .report-container {
                grid-template-columns: 1fr;
            }
            .filters form {
                flex-direction: column;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h2>Relatório de Vendas</h2>
    
    <div class="filters">
        <form method="GET">
            <div>
                <label for="tipo">Tipo de Relatório:</label>
                <select name="tipo" id="tipo">
                    <option value="diario" <?= $tipo_relatorio == 'diario' ? 'selected' : '' ?>>Diário</option>
                    <option value="mensal" <?= $tipo_relatorio == 'mensal' ? 'selected' : '' ?>>Mensal</option>
                    <option value="categoria" <?= $tipo_relatorio == 'categoria' ? 'selected' : '' ?>>Por Categoria</option>
                </select>
            </div>
            
            <div>
                <label for="data_inicio">Data Início:</label>
                <input type="date" name="data_inicio" id="data_inicio" value="<?= $data_inicio ?>">
            </div>
            
            <div>
                <label for="data_fim">Data Fim:</label>
                <input type="date" name="data_fim" id="data_fim" value="<?= $data_fim ?>">
            </div>
            
            <button type="submit">Gerar Relatório</button>
        </form>
    </div>
    
    <div class="report-container">
        <div class="chart-container">
            <canvas id="vendasChart"></canvas>
        </div>
        
        <div class="metrics">
            <h3>Resumo do Período</h3>
            
            <div class="metric-card">
                <div class="metric-title">Total de Vendas</div>
                <div class="metric-value">R$ <?= number_format($total_periodo, 2, ',', '.') ?></div>
            </div>
            
            <div class="metric-card">
                <div class="metric-title">Total de Pedidos</div>
                <div class="metric-value"><?= $total_pedidos ?></div>
            </div>
            
            <div class="metric-card">
                <div class="metric-title">Ticket Médio</div>
                <div class="metric-value">R$ <?= number_format($ticket_medio, 2, ',', '.') ?></div>
            </div>
            
            <div class="metric-card">
                <div class="metric-title">Média <?= $tipo_relatorio == 'diario' ? 'Diária' : ($tipo_relatorio == 'mensal' ? 'Mensal' : 'por Categoria') ?></div>
                <div class="metric-value">R$ <?= number_format($media_periodo, 2, ',', '.') ?></div>
            </div>
        </div>
    </div>
    
    <h3>Produtos Mais Vendidos no Período</h3>
    <?php if (empty($produtos_mais_vendidos)): ?>
        <p>Nenhum produto vendido no período selecionado.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Produto</th>
                <th>Quantidade Vendida</th>
                <th>Valor Total</th>
            </tr>
            <?php foreach ($produtos_mais_vendidos as $produto): ?>
            <tr>
                <td><?= htmlspecialchars($produto['nome']) ?></td>
                <td><?= $produto['total_vendas'] ?></td>
                <td>R$ <?= number_format($produto['valor_total'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    
    <a href="admins_dashboard.php" class="btn-back">Voltar para o Dashboard</a>
    
    <script>
        // Configuração do gráfico
        const ctx = document.getElementById('vendasChart').getContext('2d');
        const vendasChart = new Chart(ctx, {
            type: '<?= $tipo_relatorio == 'categoria' ? 'bar' : 'line' ?>',
            data: {
                labels: <?= $json_labels ?>,
                datasets: [{
                    label: 'Vendas (R$)',
                    data: <?= $json_dados ?>,
                    backgroundColor: 'rgba(76, 175, 80, 0.2)',
                    borderColor: 'rgba(76, 175, 80, 1)',
                    borderWidth: 1,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Relatório de Vendas - <?= date('d/m/Y', strtotime($data_inicio)) ?> a <?= date('d/m/Y', strtotime($data_fim)) ?>'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'R$ ' + context.raw.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>