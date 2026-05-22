<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admins_login.php");
    exit;
}

// Habilitar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$usuario = $_SESSION['admin'];
// Use caminho absoluto para o arquivo
$admins_file = __DIR__ . "/admins.json";
$admins = json_decode(file_get_contents($admins_file), true);
$erro = '';
$sucesso = '';

// Verificar se o JSON foi carregado corretamente
if ($admins === null) {
    $erro = "Erro ao carregar dados de administradores: " . json_last_error_msg();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nova = $_POST['nova_senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';

    if (strlen($nova) < 6) {
        $erro = "A nova senha deve ter pelo menos 6 caracteres.";
    } elseif ($nova !== $confirmar) {
        $erro = "As senhas não coincidem.";
    } else {
        try {
            // Atualizar senha com hash
            $admins[$usuario]['senha'] = password_hash($nova, PASSWORD_DEFAULT);
            $admins[$usuario]['primeiro_acesso'] = false;
            
            // Salvar alterações no arquivo
            $resultado = file_put_contents($admins_file, json_encode($admins, JSON_PRETTY_PRINT));
            
            // Verificar se o arquivo foi salvo corretamente
            if ($resultado === false) {
                throw new Exception("Não foi possível salvar as alterações no arquivo.");
            }

            $sucesso = "Senha alterada com sucesso!";
            
            // Verificar o conteúdo do arquivo após a gravação
            $verificacao = json_decode(file_get_contents($admins_file), true);
            if ($verificacao === null) {
                $erro = "Erro na verificação do JSON após gravação: " . json_last_error_msg();
                $sucesso = ""; // Limpar mensagem de sucesso
            } else {
                // Adicionar log para debug
                error_log("Senha alterada para o usuário $usuario - primeiro_acesso: " . 
                         ($verificacao[$usuario]['primeiro_acesso'] ? 'true' : 'false'));
                
                // Redirecionar após 2 segundos
                header("Refresh: 2; URL=admins_dashboard.php");
                exit;
            }
        } catch (Exception $e) {
            $erro = "Erro ao alterar senha: " . $e->getMessage();
            $sucesso = ""; // Limpar mensagem de sucesso
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Trocar Senha</title>
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
            max-width: 400px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
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
    </style>
</head>
<body>
    <h2>Primeiro Acesso – Troque sua Senha</h2>
    
    <form method="post">
        <label>Nova Senha: <input type="password" name="nova_senha" required></label>
        <label>Confirmar Senha: <input type="password" name="confirmar_senha" required></label>
        <button type="submit">Salvar</button>
        
        <?php if ($erro): ?>
            <p class="error"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>
        
        <?php if ($sucesso): ?>
            <p class="success"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>
    </form>
</body>
</html>