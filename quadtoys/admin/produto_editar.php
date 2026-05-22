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

// Inicializar variáveis
$erro = '';
$sucesso = '';

// Buscar dados do produto
try {
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE produto_id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$produto) {
        echo "Produto não encontrado!";
        exit;
    }
} catch(PDOException $e) {
    echo "Erro ao buscar produto: " . $e->getMessage();
    exit;
}

// Processar formulário de edição
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
            // Atualizar produto
            $stmt = $conn->prepare("UPDATE produtos SET 
                                   nome = ?, 
                                   descricao = ?, 
                                   preco = ?, 
                                   estoque = ?, 
                                   categoria_id = ? 
                                   WHERE produto_id = ?");
            $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria_id ?: null, $id]);
            
            $sucesso = "Produto atualizado com sucesso!";
            
            // Atualizar dados exibidos
            $produto['nome'] = $nome;
            $produto['descricao'] = $descricao;
            $produto['preco'] = $preco;
            $produto['estoque'] = $estoque;
            $produto['categoria_id'] = $categoria_id;
            
            // Redirecionar após 2 segundos
            header("Refresh: 2; URL=produto_listar.php");
        } catch(PDOException $e) {
            $erro = "Erro ao atualizar produto: " . $e->getMessage();
        }
    }
}

// Buscar categorias para o select
try {
    $stmt = $conn->prepare("SELECT categoria_id, nome FROM categorias WHERE ativo = 1 ORDER BY nome");
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
    <title>Editar Produto</title>
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
    <h2>Editar Produto</h2>
    
    <?php if(!empty($erro)): ?>
        <p class="error"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    
    <?php if(!empty($sucesso)): ?>
        <p class="success"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>
    
    <form method="post">
        <label>Nome:
            <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>
        </label>
        
        <label>Descrição:
            <textarea name="descricao"><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea>
        </label>
        
        <label>Preço:
            <input type="number" name="preco" value="<?= $produto['preco'] ?>" step="0.01" min="0.01" required>
        </label>
        
        <label>Estoque:
            <input type="number" name="estoque" value="<?= $produto['estoque'] ?>" min="0" required>
        </label>
        
        <label>Categoria:
            <select name="categoria_id">
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['categoria_id'] ?>" <?= $produto['categoria_id'] == $cat['categoria_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        
        <button type="submit">Atualizar Produto</button>
    </form>
    
    <a href="produto_listar.php">Voltar para Lista de Produtos</a>
</body>
</html>