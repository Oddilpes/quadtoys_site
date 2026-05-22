<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admins_login.php");
    exit;
}

// Incluir arquivo de configuração
require_once __DIR__ . '/../includes/config.php';

// Definir variáveis de paginação
$registros_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Filtros
$busca = $_GET['busca'] ?? '';
$status = $_GET['status'] ?? '';

// Construir a consulta
$sql = "SELECT * FROM clientes WHERE 1=1";
$params = [];

if (!empty($busca)) {
    $sql .= " AND (nome LIKE ? OR email LIKE ? OR telefone LIKE ?)";
    $params[] = "%$busca%";
    $params[] = "%$busca%";
    $params[] = "%$busca%";
}

if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY data_cadastro DESC LIMIT $offset, $registros_por_pagina";

// Executar consulta
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Contar total de registros para paginação
    $sql_count = "SELECT COUNT(*) FROM clientes WHERE 1=1";
    
    if (!empty($busca)) {
        $sql_count .= " AND (nome LIKE ? OR email LIKE ? OR telefone LIKE ?)";
    }
    
    if (!empty($status)) {
        $sql_count .= " AND status = ?";
    }
    
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->execute($params);
    $total_registros = $stmt_count->fetchColumn();
    $total_paginas = ceil($total_registros / $registros_por_pagina);
    
    // Buscar status disponíveis para o filtro
    $stmt_status = $conn->query("SELECT DISTINCT status FROM clientes ORDER BY status");
    $status_disponiveis = $stmt_status->fetchAll(PDO::FETCH_COLUMN);
    
} catch(PDOException $e) {
    echo "Erro ao buscar clientes: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Clientes</title>
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
        .status-ativo { background-color: #4CAF50; color: white; }
        .status-inativo { background-color: #F44336; color: white; }
        .status-suspenso { background-color: #FF9800; color: white; }
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
    <h2>Gerenciar Clientes</h2>
    
    <div class="filters">
        <form method="GET">
            <div>
                <label for="busca">Buscar:</label>
                <input type="text" name="busca" id="busca" placeholder="Nome, email ou telefone" value="<?= htmlspecialchars($busca) ?>">
            </div>
            
            <div>
                <label for="status">Status:</label>
                <select name="status" id="status">
                    <option value="">Todos</option>
                    <?php foreach ($status_disponiveis as $s): ?>
                        <option value="<?= $s ?>" <?= $status == $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit">Filtrar</button>
            <button type="button" onclick="window.location.href='cliente_listar.php'">Limpar Filtros</button>
        </form>
    </div>
    
    <?php if (empty($clientes)): ?>
        <div class="empty-message">
            <p>Nenhum cliente encontrado com os filtros aplicados.</p>
        </div>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Data de Cadastro</th>
                <th>Último Acesso</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><?= $cliente['cliente_id'] ?></td>
                <td><?= htmlspecialchars($cliente['nome']) ?></td>
                <td><?= htmlspecialchars($cliente['email']) ?></td>
                <td><?= htmlspecialchars($cliente['telefone'] ?? '-') ?></td>
                <td><?= date('d/m/Y', strtotime($cliente['data_cadastro'])) ?></td>
                <td><?= $cliente['ultimo_acesso'] ? date('d/m/Y H:i', strtotime($cliente['ultimo_acesso'])) : '-' ?></td>
                <td>
                    <span class="status status-<?= strtolower($cliente['status']) ?>">
                        <?= $cliente['status'] ?>
                    </span>
                </td>
                <td class="actions">
                    <a href="cliente_detalhes.php?id=<?= $cliente['cliente_id'] ?>">Detalhes</a>
                    <a href="cliente_editar.php?id=<?= $cliente['cliente_id'] ?>">Editar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <?php if ($total_paginas > 1): ?>
        <div class="pagination">
            <?php if ($pagina_atual > 1): ?>
                <a href="?pagina=<?= $pagina_atual - 1 ?>&busca=<?= urlencode($busca) ?>&status=<?= urlencode($status) ?>">Anterior</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <?php if ($i == $pagina_atual): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?pagina=<?= $i ?>&busca=<?= urlencode($busca) ?>&status=<?= urlencode($status) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($pagina_atual < $total_paginas): ?>
                <a href="?pagina=<?= $pagina_atual + 1 ?>&busca=<?= urlencode($busca) ?>&status=<?= urlencode($status) ?>">Próxima</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <a href="admins_dashboard.php" class="btn-back">Voltar para o Dashboard</a>
</body>
</html>