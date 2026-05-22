<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admins_login.php");
    exit;
}

// Incluir arquivo de configuração
require_once __DIR__ . '/../includes/config.php';

// Definir variáveis de paginação
$registros_por_pagina = 10; // Corrigido de "a10" para "10"
$pagina_atual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Filtros
$status = $_GET['status'] ?? '';
$cliente = $_GET['cliente'] ?? '';

// Construir a consulta
$sql = "SELECT p.*, c.nome as cliente_nome 
        FROM pedidos p
        JOIN clientes c ON p.cliente_id = c.cliente_id
        WHERE 1=1";
$params = [];

if (!empty($status)) {
    $sql .= " AND p.status = ?";
    $params[] = $status;
}

if (!empty($cliente)) {
    $sql .= " AND c.nome LIKE ?";
    $params[] = "%$cliente%";
}

$sql .= " ORDER BY p.data_pedido DESC LIMIT $offset, $registros_por_pagina";

// Executar consulta
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Contar total de registros para paginação
    $sql_count = "SELECT COUNT(*) FROM pedidos p
                 JOIN clientes c ON p.cliente_id = c.cliente_id
                 WHERE 1=1";
    
    if (!empty($status)) {
        $sql_count .= " AND p.status = ?";
    }
    
    if (!empty($cliente)) {
        $sql_count .= " AND c.nome LIKE ?";
    }
    
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->execute($params);
    $total_registros = $stmt_count->fetchColumn();
    $total_paginas = ceil($total_registros / $registros_por_pagina);
    
    // Buscar status disponíveis para o filtro
    $stmt_status = $conn->query("SELECT DISTINCT status FROM pedidos ORDER BY status");
    $status_disponiveis = $stmt_status->fetchAll(PDO::FETCH_COLUMN);
    
} catch(PDOException $e) {
    echo "Erro ao buscar pedidos: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Pedidos</title>
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
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        .pagination .active {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pendente { background-color: #FFC107; color: #000; }
        .status-pago { background-color: #2196F3; color: white; }
        .status-enviado { background-color: #9C27B0; color: white; }
        .status-entregue { background-color: #4CAF50; color: white; }
        .status-cancelado { background-color: #F44336; color: white; }
        .actions a {
            margin-right: 10px;
            color: #4CAF50;
            text-decoration: none;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .empty-message {
            padding: 20px;
            text-align: center;
            background: white;
            border-radius: 4px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
    <h2>Gerenciar Pedidos</h2>
    
    <div class="filters">
        <form method="GET">
            <div>
                <label for="status">Filtrar por Status:</label>
                <select name="status" id="status">
                    <option value="">Todos</option>
                    <?php foreach ($status_disponiveis as $s): ?>
                        <option value="<?= $s ?>" <?= $status == $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="cliente">Cliente:</label>
                <input type="text" name="cliente" id="cliente" value="<?= htmlspecialchars($cliente) ?>">
            </div>
            
            <button type="submit">Filtrar</button>
            <button type="button" onclick="window.location.href='pedido_listar.php'">Limpar Filtros</button>
        </form>
    </div>
    
    <?php if (empty($pedidos)): ?>
        <div class="empty-message">
            <p>Nenhum pedido encontrado com os filtros aplicados.</p>
        </div>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Status</th>
                <th>Valor Total</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($pedidos as $pedido): ?>
            <tr>
                <td><?= $pedido['pedido_id'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></td>
                <td><?= htmlspecialchars($pedido['cliente_nome']) ?></td>
                <td>
                    <span class="status status-<?= strtolower($pedido['status']) ?>">
                        <?= $pedido['status'] ?>
                    </span>
                </td>
                <td>R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></td>
                <td class="actions">
                    <a href="pedido_detalhes.php?id=<?= $pedido['pedido_id'] ?>">Detalhes</a>
                    <a href="pedido_editar.php?id=<?= $pedido['pedido_id'] ?>">Editar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <?php if ($total_paginas > 1): ?>
        <div class="pagination">
            <?php if ($pagina_atual > 1): ?>
                <a href="?pagina=<?= $pagina_atual - 1 ?>&status=<?= urlencode($status) ?>&cliente=<?= urlencode($cliente) ?>">Anterior</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <?php if ($i == $pagina_atual): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?pagina=<?= $i ?>&status=<?= urlencode($status) ?>&cliente=<?= urlencode($cliente) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($pagina_atual < $total_paginas): ?>
                <a href="?pagina=<?= $pagina_atual + 1 ?>&status=<?= urlencode($status) ?>&cliente=<?= urlencode($cliente) ?>">Próxima</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <a href="admins_dashboard.php" class="btn-back">Voltar para o Dashboard</a>
</body>
</html>