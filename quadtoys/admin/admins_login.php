<?php
session_start();
if (isset($_SESSION['admin'])) {
    header("Location: admins_dashboard.php");
    exit;
}

$admins_file = __DIR__ . "/admins.json";
$erro = '';

if (!file_exists($admins_file)) {
    die('<p style="color:red;font-family:Arial;padding:20px">Erro: arquivo admins.json não encontrado em ' . htmlspecialchars($admins_file) . '</p>');
}

$admins = json_decode(file_get_contents($admins_file), true);
if ($admins === null) {
    die('<p style="color:red;font-family:Arial;padding:20px">Erro ao ler admins.json: ' . json_last_error_msg() . '</p>');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha'] ?? '';

    if (isset($admins[$usuario])) {
        $dados = $admins[$usuario];

        // Segurança: nunca permite login se a senha estiver vazia no arquivo
        if (empty($dados['senha'])) {
            $erro = "Usuário ou senha inválidos.";
        } else {
        $senha_correta = $dados['primeiro_acesso']
            ? ($senha === $dados['senha'])
            : password_verify($senha, $dados['senha']);

        if ($senha_correta) {
            session_regenerate_id(true);
            $_SESSION['admin'] = $usuario;
            header($dados['primeiro_acesso']
                ? "Location: trocar_senha.php"
                : "Location: admins_dashboard.php");
            exit;
        }
        } // end else (senha não vazia)
    }
    $erro = "Usuário ou senha inválidos.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Admin – QuadToys</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Syne:wght@600;800&family=DM+Sans:wght@400;500&display=swap');
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
  body{min-height:100vh;background:#0d1117;display:flex;align-items:center;
       justify-content:center;font-family:'DM Sans',sans-serif;padding:20px;
       position:relative;overflow:hidden}
  body::before{content:'';position:absolute;inset:0;
    background-image:linear-gradient(rgba(76,175,80,.04) 1px,transparent 1px),
                     linear-gradient(90deg,rgba(76,175,80,.04) 1px,transparent 1px);
    background-size:40px 40px;pointer-events:none}
  .card{background:#161b22;border:1px solid #30363d;border-radius:16px;
        width:100%;max-width:420px;padding:44px 38px;
        box-shadow:0 16px 48px rgba(0,0,0,.6);position:relative;z-index:1;
        animation:fadeUp .4s ease both}
  @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
  .logo{font-family:'Syne',sans-serif;font-weight:800;font-size:1.7rem;
        color:#4CAF50;letter-spacing:-1px;line-height:1}
  .logo span{color:#e6edf3}
  .tag{display:inline-flex;align-items:center;gap:6px;margin-top:10px;
       background:#1a2e1a;color:#4ade80;border:1px solid #2d4a2d;
       padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:500}
  .tag::before{content:'';width:6px;height:6px;border-radius:50%;
    background:#4CAF50;animation:pulse 2s ease infinite}
  @keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
  .logo-area{margin-bottom:32px}
  h2{font-family:'Syne',sans-serif;font-size:1.2rem;color:#e6edf3;margin-bottom:6px}
  .welcome{font-size:.85rem;color:#8b949e;margin-bottom:26px}
  .alert-err{display:flex;align-items:center;gap:10px;background:#2d1a1a;
             border:1px solid #7f1d1d;color:#fca5a5;border-radius:8px;
             padding:12px 14px;font-size:.875rem;margin-bottom:20px;
             animation:shake .35s ease}
  @keyframes shake{0%,100%{transform:translateX(0)}20%{transform:translateX(-6px)}
    40%{transform:translateX(6px)}60%{transform:translateX(-4px)}80%{transform:translateX(4px)}}
  .field{margin-bottom:18px}
  label{display:block;font-size:.78rem;color:#8b949e;font-weight:500;
        margin-bottom:7px;letter-spacing:.3px;text-transform:uppercase}
  .input-wrap{position:relative}
  .input-wrap .icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);
    color:#484f58;font-size:.95rem;pointer-events:none}
  input[type="text"],input[type="password"]{width:100%;
    padding:11px 14px 11px 40px;background:#0d1117;border:1px solid #30363d;
    border-radius:8px;color:#e6edf3;font-size:.95rem;font-family:'DM Sans',sans-serif;
    outline:none;transition:border-color .2s,box-shadow .2s}
  input:focus{border-color:#4CAF50;box-shadow:0 0 0 3px rgba(76,175,80,.12)}
  .btn{width:100%;padding:12px;background:#4CAF50;color:#fff;border:none;
       border-radius:8px;font-size:1rem;font-weight:600;font-family:'DM Sans',sans-serif;
       cursor:pointer;margin-top:6px;transition:background .2s,transform .1s}
  .btn:hover{background:#43a047}.btn:active{transform:scale(.98)}
  hr{border:none;border-top:1px solid #21262d;margin:24px 0 18px}
  .back{text-align:center;font-size:.83rem;color:#8b949e}
  .back a{color:#4CAF50;text-decoration:none;font-weight:500}
</style>
</head>
<body>
<div class="card">
  <div class="logo-area">
    <div class="logo">Quad<span>Toys</span></div>
    <div class="tag">Painel Administrativo</div>
  </div>
  <h2>Bem-vindo, Admin</h2>
  <p class="welcome">Acesso restrito. Somente administradores autorizados.</p>
  <?php if ($erro): ?>
    <div class="alert-err"><span>⚠</span> <?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>
  <form method="POST" autocomplete="off">
    <div class="field">
      <label>Usuário</label>
      <div class="input-wrap">
        <span class="icon">👤</span>
        <input type="text" name="usuario"
               value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
               placeholder="seu usuário" required autofocus>
      </div>
    </div>
    <div class="field">
      <label>Senha</label>
      <div class="input-wrap">
        <span class="icon">🔒</span>
        <input type="password" name="senha" placeholder="••••••••" required>
      </div>
    </div>
    <button type="submit" class="btn">Entrar no Painel</button>
  </form>
  <hr>
  <p class="back"><a href="../index.php">← Voltar à loja</a></p>
</div>
</body>
</html>
