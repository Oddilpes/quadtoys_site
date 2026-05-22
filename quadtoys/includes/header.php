<?php
// O config.php cuida da conexão e do session_start()
require_once __DIR__ . '/config.php';

// Pega o nome do arquivo atual para destacar o item ativo no menu
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuadToys - Colecionáveis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">Quad<span>Toys</span></a>

                <nav>
                    <ul>
                        <li><a href="index.php" class="<?= $paginaAtual === 'index.php' ? 'active' : '' ?>">Início</a></li>
                        <li><a href="categorias.php" class="<?= $paginaAtual === 'categorias.php' ? 'active' : '' ?>">Categorias</a></li>
                        <li><a href="colecoes.php" class="<?= $paginaAtual === 'colecoes.php' ? 'active' : '' ?>">Coleções</a></li>
                        <li><a href="destaques.php" class="<?= $paginaAtual === 'destaques.php' ? 'active' : '' ?>">Destaques</a></li>
                        <li><a href="comunidade.php" class="<?= $paginaAtual === 'comunidade.php' ? 'active' : '' ?>">Comunidade</a></li>
                        <li><a href="contato.php" class="<?= $paginaAtual === 'contato.php' ? 'active' : '' ?>">Contato</a></li>
                    </ul>
                </nav>

                <div class="search-login">
                    <div class="user-actions">
                        <?php if (isset($_SESSION['cliente_id'])): ?>
                            <span style="color:#ecf0f1; font-size:0.95rem;">
                                Olá, <strong><?= htmlspecialchars(explode(' ', $_SESSION['nome'])[0]) ?></strong>
                            </span>
                            <a href="logout.php" class="btn">Sair</a>
                        <?php else: ?>
                            <a href="login.php" class="btn">Entrar</a>
                            <a href="cadastro.php" class="btn btn-secondary">Cadastrar</a>
                        <?php endif; ?>

                        <div class="cart-icon">
                            <button id="cart-toggle" aria-label="Abrir carrinho">🛒</button>
                            <span id="cart-count">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>
