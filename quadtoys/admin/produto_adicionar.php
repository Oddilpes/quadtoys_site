<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admins_login.php");
    exit;
}

// Incluir arquivo de configuração
require_once __DIR__ . '/../includes/config.php';

// Inicializar variáveis
$nome = '';
$preco = '';
$estoque = '';
$categoria_id = '';
$descricao = '';
$erro = '';
$sucesso = '';

// Processar formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter e validar dados
    $nome = trim($_POST["nome"] ?? '');
    $preco = floatval($_POST["preco"] ?? 0);
    $estoque = intval($_POST["estoque"] ?? 0);
    $categoria_id = intval($_POST["categoria_id"] ?? 0);
    $descricao = trim($_POST["descricao"] ?? '');
    
    // Validar dados
    if (empty($nome)) {
        $erro = "Nome do produto é obrigatório.";
    } elseif ($preco <= 0) {
        $erro = "Preço deve ser maior que zero.";
    } elseif ($estoque < 0) {
        $erro = "Estoque não pode ser negativo.";
    } else {
        try {
            // MÉTODO 1: Tentar deixar o produto_id automático
            // Inserir novo produto (sem especificar produto_id, presumindo que é auto_increment)
            $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, categoria_id, data_cadastro) 
                               VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria_id ?: null]);
            
            // Se chegarmos até aqui sem erro, a inserção funcionou
            $sucesso = "Produto adicionado com sucesso!";
            // Limpar campos após sucesso
            $nome = '';
            $preco = '';
            $estoque = '';
            $categoria_id = '';
            $descricao = '';
            
            // Redirecionar após 2 segundos
            header("Refresh: 2; URL=produto_listar.php");
        } catch(PDOException $e) {
            // Se o método 1 falhou, vamos tentar o método 2
            try {
                // MÉTODO 2: Obter o próximo ID manualmente
                $stmt = $conn->query("SELECT MAX(produto_id) + 1 as next_id FROM produtos");
                $next_id = $stmt->fetchColumn();
                
                // Se não houver registros ainda, começar com ID 1
                if (!$next_id) {
                    $next_id = 1;
                }
                
                // Inserir novo produto especificando o produto_id
                $stmt = $conn->prepare("INSERT INTO produtos (produto_id, nome, descricao, preco, estoque, categoria_id, data_cadastro) 
                                      VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$next_id, $nome, $descricao, $preco, $estoque, $categoria_id ?: null]);
                
                $sucesso = "Produto adicionado com sucesso!";
                // Limpar campos após sucesso
                $nome = '';
                $preco = '';
                $estoque = '';
                $categoria_id = '';
                $descricao = '';
                
                // Redirecionar após 2 segundos
                header("Refresh: 2; URL=produto_listar.php");
            } catch(PDOException $e2) {
                $erro = "Erro ao adicionar produto: " . $e2->getMessage();
            }
        }
    }
}

// Buscar categorias para o select
try {
    $stmt = $conn->prepare("SELECT categoria_id, nome FROM categorias ORDER BY nome");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $erro = "Erro ao carregar categorias: " . $e->getMessage();
    $categorias = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Adicionar Produto</title>
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
        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Adicionar Produto</h2>
    
    <?php if(!empty($erro)): ?>
        <p class="error"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    
    <?php if(!empty($sucesso)): ?>
        <p class="success"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>
    
    <form method="post">
        <label>Nome:
            <input type="text" name="nome" value="<?= htmlspecialchars($nome) ?>" required>
        </label>
        
        <label>Descrição:
            <textarea name="descricao"><?= htmlspecialchars($descricao) ?></textarea>
        </label>
        
        <label>Preço:
            <input type="number" name="preco" value="<?= $preco ?>" step="0.01" min="0.01" required>
        </label>
        
        <label>Estoque:
            <input type="number" name="estoque" value="<?= $estoque ?>" min="0" required>
        </label>
        
        <label>Categoria:
            <select name="categoria_id">
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['categoria_id'] ?>" <?= $categoria_id == $cat['categoria_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        
        <button type="submit">Salvar Produto</button>
    </form>
    
    <a href="produto_listar.php">Voltar para Lista de Produtos</a>
</body>
</html>