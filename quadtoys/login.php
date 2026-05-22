<?php
require_once 'includes/config.php';

// Se já está logado, redireciona
if (isset($_SESSION['cliente_id'])) {
    header('Location: index.php');
    exit();
}

$erro = '';
$email = '';
$redirect = $_GET['redirect'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $redirect = $_POST['redirect'] ?? 'index.php';

    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos.';
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM clientes WHERE email = ? AND status = 'ativo'");
            $stmt->execute([$email]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cliente && password_verify($senha, $cliente['senha'])) {
                $_SESSION['cliente_id'] = $cliente['cliente_id'];
                $_SESSION['nome'] = $cliente['nome'];

                // Atualiza último acesso
                $up = $conn->prepare("UPDATE clientes SET ultimo_acesso = NOW() WHERE cliente_id = ?");
                $up->execute([$cliente['cliente_id']]);

                // Redireciona com segurança (só permite caminhos relativos)
                if (preg_match('/^[a-zA-Z0-9_.\-\/?=&]+$/', $redirect)) {
                    header('Location: ' . $redirect);
                } else {
                    header('Location: index.php');
                }
                exit();
            } else {
                $erro = 'E-mail ou senha incorretos.';
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao realizar login. Tente novamente.';
            error_log('Erro de login: ' . $e->getMessage());
        }
    }
}

include 'includes/header.php';
?>

<div class="container auth-container">
    <div class="auth-box">
        <h1>Entrar</h1>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" required class="form-control"
                       value="<?= htmlspecialchars($email) ?>">
            </div>

            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" required class="form-control">
            </div>

            <button type="submit" class="btn" style="width:100%; padding:12px; font-size:1.05em;">Entrar</button>
            <p style="text-align:center; margin-top:15px;">
                Não tem conta? <a href="cadastro.php" style="color:#4CAF50; font-weight:bold;">Cadastre-se</a>
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
