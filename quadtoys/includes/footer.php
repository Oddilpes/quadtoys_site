    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>QuadToys</h3>
                    <p>Sua loja de colecionáveis online. Action figures, cards, quadrinhos e muito mais.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook">f</a>
                        <a href="#" aria-label="Instagram">📷</a>
                        <a href="#" aria-label="Twitter">𝕏</a>
                    </div>
                </div>

                <div class="footer-column">
                    <h3>Navegação</h3>
                    <ul>
                        <li><a href="index.php">Início</a></li>
                        <li><a href="categorias.php">Categorias</a></li>
                        <li><a href="colecoes.php">Coleções</a></li>
                        <li><a href="destaques.php">Destaques</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Atendimento</h3>
                    <ul>
                        <li><a href="contato.php">Contato</a></li>
                        <li><a href="comunidade.php">Comunidade</a></li>
                        <li><a href="#">Política de Privacidade</a></li>
                        <li><a href="#">Termos de Uso</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Minha Conta</h3>
                    <ul>
                        <?php if (isset($_SESSION['cliente_id'])): ?>
                            <li><a href="carrinho.php">Meu Carrinho</a></li>
                            <li><a href="logout.php">Sair</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Entrar</a></li>
                            <li><a href="cadastro.php">Cadastrar</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                &copy; <?= date('Y') ?> QuadToys - Projeto Acadêmico. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <!-- Carrinho lateral (overlay) -->
    <div id="cart-overlay" class="cart-overlay">
        <div class="cart-header">
            <h2>Seu Carrinho</h2>
            <button id="close-cart" class="close-cart" aria-label="Fechar carrinho">×</button>
        </div>
        <div id="cart-items" class="cart-items">
            <p class="empty-cart-message">Seu carrinho está vazio</p>
        </div>
        <div class="cart-total">
            <span>Total:</span>
            <span id="cart-total-price">R$ 0,00</span>
        </div>
        <button id="checkout-btn">Finalizar Compra</button>
    </div>

    <script src="script.js" defer></script>
</body>
</html>
