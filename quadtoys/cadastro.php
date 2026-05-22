<?php
require_once 'includes/config.php';

if (isset($_SESSION['cliente_id'])) {
    header('Location: index.php');
    exit();
}

$nome = $email = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = 'Todos os campos são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem.';
    } else {
        try {
            $stmt = $conn->prepare("SELECT cliente_id FROM clientes WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $erro = 'Este e-mail já está cadastrado.';
            } else {
                $hash_senha = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conn->prepare(
                    "INSERT INTO clientes (nome, email, senha, data_cadastro, ultimo_acesso, status)
                     VALUES (?, ?, ?, NOW(), NOW(), 'ativo')"
                );
                $stmt->execute([$nome, $email, $hash_senha]);

                $cliente_id = $conn->lastInsertId();
                $_SESSION['cliente_id'] = $cliente_id;
                $_SESSION['nome'] = $nome;

                header('Location: index.php');
                exit();
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao cadastrar. Tente novamente.';
            error_log('Erro cadastro: ' . $e->getMessage());
        }
    }
}

include 'includes/header.php';
?>

<div class="container auth-container">
    <div class="auth-box">
        <h1>Criar Conta</h1>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" required class="form-control"
                       value="<?= htmlspecialchars($nome) ?>">
            </div>

            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" required class="form-control"
                       value="<?= htmlspecialchars($email) ?>">
            </div>

            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" required class="form-control" minlength="6">
                <small style="color:#7f8c8d; font-size:0.85em;">Pelo menos 6 caracteres</small>
            </div>

            <div class="form-group">
                <label>Confirmar Senha</label>
                <input type="password" name="confirmar_senha" required class="form-control" minlength="6">
            </div>

            <button type="submit" class="btn" style="width:100%; padding:12px; font-size:1.05em;">Cadastrar</button>
            <p style="text-align:center; margin-top:15px;">
                Já tem conta? <a href="login.php" style="color:#4CAF50; font-weight:bold;">Faça login</a>
            </p>
        </form>
    </div>
</div>

<style>
    .auth-container { padding: 50px 0; }
    .auth-box {
        max-width: 420px;
        margin: 0 auto;
        background: white;
        padding: 35px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .auth-box h1 { color: #2c3e50; margin-bottom: 25px; text-align: center; }
    .form-group { margin-bottom: 18px; }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: #555;
    }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1em;
    }
    .form-control:focus { outline: none; border-color: #4CAF50; }
    .alert {
        padding: 12px 15px;
        border-radius: 4px;
        margin-bottom: 18px;
    }
    .alert-danger {
        background: #fee;
        color: #c0392b;
        border: 1px solid #f5b7b1;
    }
</style>

<?php include 'includes/footer.php'; ?>
