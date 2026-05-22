<?php include 'includes/header.php'; ?>

<style>
    /* Botão Explorar Coleções */
    a.btn-explore {
        display: inline-block;
        background-color: #4CAF50;
        color: white;
        font-size: 1.1em;
        padding: 12px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    a.btn-explore:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        background-color: #45a049;
    }
</style>

<section class="hero">
    <div class="container">
        <h1>O Universo dos Colecionáveis em um só lugar</h1>
        <p>Descubra itens colecionáveis raros e únicos.
           Junte-se a milhares de colecionadores apaixonados.</p>
        <div class="hero-buttons">
            <a href="colecoes.php" class="btn-explore">Explorar Coleções</a>
        </div>
    </div>
</section>

<section class="categories">
    <div class="container">
        <h2 class="section-title">Categorias Populares</h2>
        <div class="categories-grid">
            <?php
            // Buscar 4 categorias direto do banco (mais dinâmico que hardcode)
            try {
                $stmt = $conn->query("SELECT * FROM categorias WHERE ativo = 1 ORDER BY categoria_id LIMIT 4");
                $cats_destaque = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $cats_destaque = [];
            }
            foreach ($cats_destaque as $cat):
            ?>
                <div class="category-card">
                    <div class="category-img">
                        <img src="images/categoria_<?= $cat['categoria_id'] ?>.jpg"
                             onerror="this.onerror=null;this.src='images/placeholder.jpg';"
                             alt="<?= htmlspecialchars($cat['nome']) ?>">
                    </div>
                    <div class="category-content">
                        <h3><?= htmlspecialchars($cat['nome']) ?></h3>
                        <p><?= htmlspecialchars($cat['descricao'] ?? '') ?></p>
                        <a href="produtos_por_categoria.php?id=<?= $cat['categoria_id'] ?>" style="color: #e74c3c;">Ver coleção →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="feature-products">
    <div class="container">
        <h2 class="section-title">Itens em Destaque</h2>
        <div class="products-grid">
            <?php
            // Produtos em destaque: usar a flag `destaque` da tabela, com fallback para 4 mais recentes
            try {
                $stmt = $conn->query("SELECT * FROM produtos WHERE destaque = 1 AND estoque > 0 ORDER BY data_cadastro DESC LIMIT 4");
                $destaques = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($destaques) === 0) {
                    $stmt = $conn->query("SELECT * FROM produtos WHERE estoque > 0 ORDER BY data_cadastro DESC LIMIT 4");
                    $destaques = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (PDOException $e) {
                $destaques = [];
            }
            foreach ($destaques as $p):
            ?>
                <div class="product-card"
                     data-id="<?= $p['produto_id'] ?>"
                     data-name="<?= htmlspecialchars($p['nome']) ?>"
                     data-price="<?= $p['preco'] ?>">
                    <div class="product-img">
                        <img src="images/produtos/produto_<?= $p['produto_id'] ?>.jpg"
                             onerror="this.onerror=null;this.src='images/placeholder.jpg';"
                             alt="<?= htmlspecialchars($p['nome']) ?>">
                        <?php if ($p['estoque'] <= 5): ?>
                            <div class="product-badge">RARO</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-content">
                        <h3><?= htmlspecialchars($p['nome']) ?></h3>
                        <div class="price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
                        <div class="meta">
                            <span>Estoque: <?= $p['estoque'] ?></span>
                        </div>
                        <button class="add-to-cart btn">Adicionar ao Carrinho</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="call-to-action">
    <div class="container">
        <h2>Fique por dentro das novidades!</h2>
        <p>Receba alertas sobre itens raros, promoções exclusivas e dicas para colecionadores.</p>
        <form class="newsletter" onsubmit="alert('Inscrição registrada! (demo)'); return false;">
            <input type="email" placeholder="Seu melhor e-mail" required>
            <button type="submit">Inscrever-se</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
