<?php
require_once 'includes/config.php';

try {
    $stmt = $conn->query("SELECT * FROM categorias WHERE ativo = 1 ORDER BY nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erro ao buscar categorias: ' . htmlspecialchars($e->getMessage()));
}

include 'includes/header.php';
?>

<div class="container" style="padding: 30px 0;">
    <a href="index.php" class="btn-voltar">← Voltar para a Página Inicial</a>

    <h1 style="margin: 20px 0; color: #2c3e50;">Categorias</h1>

    <?php if (count($categorias) > 0): ?>
        <div class="categoria-lista">
            <?php foreach ($categorias as $cat): ?>
                <div class="categoria-card">
                    <a href="produtos_por_categoria.php?id=<?= $cat['categoria_id'] ?>">
                        <h3><?= htmlspecialchars($cat['nome']) ?></h3>
                        <?php if (!empty($cat['descricao'])): ?>
                            <p><?= htmlspecialchars($cat['descricao']) ?></p>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Nenhuma categoria cadastrada no momento.</p>
    <?php endif; ?>
</div>

<style>
    .btn-voltar {
        display: inline-block;
        padding: 10px 20px;
        background-color: #2c3e50;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
    }
    .btn-voltar:hover {
        background-color: #34495e;
    }
    .categoria-lista {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }
    .categoria-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .categoria-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }
    .categoria-card a {
        display: block;
        padding: 25px 20px;
        text-decoration: none;
        color: inherit;
    }
    .categoria-card h3 {
        color: #e74c3c;
        font-size: 1.3em;
        margin-bottom: 8px;
    }
    .categoria-card p {
        color: #777;
        font-size: 0.9em;
    }
</style>

<?php include 'includes/footer.php'; ?>
