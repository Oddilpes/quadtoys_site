document.addEventListener("DOMContentLoaded", () => {
    const cartOverlay = document.getElementById("cart-overlay");
    const cartToggle = document.getElementById("cart-toggle");
    const closeCart = document.getElementById("close-cart");
    const cartItemsContainer = document.getElementById("cart-items");
    const cartTotalPrice = document.getElementById("cart-total-price");
    const cartCount = document.getElementById("cart-count");
    const checkoutBtn = document.getElementById("checkout-btn");

    let cart = [];

    function checkLogin() {
        return fetch('check_login.php')
            .then(r => r.json())
            .then(d => d.logged_in)
            .catch(() => false);
    }

    if (cartToggle && cartOverlay) {
        cartToggle.addEventListener("click", (e) => {
            e.stopPropagation();
            cartOverlay.classList.add("open");
            updateCartFromServer();
        });
    }

    if (closeCart && cartOverlay) {
        closeCart.addEventListener("click", () => {
            cartOverlay.classList.remove("open");
        });
    }

    function updateCartFromServer() {
        checkLogin().then(isLoggedIn => {
            if (!isLoggedIn) {
                updateCartUI([]);
                return;
            }
            fetch('get_cart.php')
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        cart = data.items;
                        updateCartUI(cart);
                    }
                })
                .catch(err => console.error('Erro ao carregar carrinho:', err));
        });
    }

    function addToCartServer(item) {
        checkLogin().then(isLoggedIn => {
            if (!isLoggedIn) {
                if (confirm('Você precisa estar logado para adicionar ao carrinho. Ir para o login?')) {
                    window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
                }
                return;
            }

            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(item)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    updateCartFromServer();
                    showToast('✓ ' + (data.message || 'Adicionado ao carrinho!'));
                } else {
                    if (data.need_login) {
                        window.location.href = 'login.php';
                    } else {
                        showToast('✗ ' + (data.message || 'Erro ao adicionar'), true);
                    }
                }
            })
            .catch(err => {
                console.error('Erro na requisição:', err);
                showToast('Erro de conexão', true);
            });
        });
    }

    function removeFromCartServer(id) {
        fetch(`remove_from_cart.php?id=${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) updateCartFromServer();
            })
            .catch(err => console.error('Erro:', err));
    }

    function updateCartUI(items) {
        if (!cartItemsContainer) return;

        cartItemsContainer.innerHTML = "";
        let total = 0;
        let count = 0;

        if (items.length === 0) {
            cartItemsContainer.innerHTML = '<p class="empty-cart-message">Seu carrinho está vazio</p>';
        } else {
            items.forEach(item => {
                const cartItem = document.createElement("div");
                cartItem.classList.add("cart-item");
                cartItem.innerHTML = `
                    <div style="flex:1;">
                        <div style="font-weight:500;">${escapeHtml(item.nome)}</div>
                        <div style="color:#777; font-size:0.9em;">${item.quantidade} × R$ ${parseFloat(item.preco).toFixed(2).replace('.', ',')}</div>
                    </div>
                    <div style="font-weight:bold; margin: 0 10px;">R$ ${(parseFloat(item.preco) * item.quantidade).toFixed(2).replace('.', ',')}</div>
                    <button class="remove-item" data-id="${item.carrinho_id}" aria-label="Remover">×</button>
                `;
                cartItemsContainer.appendChild(cartItem);
                total += parseFloat(item.preco) * item.quantidade;
                count += parseInt(item.quantidade);
            });
        }

        if (cartTotalPrice) {
            cartTotalPrice.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }
        if (cartCount) {
            cartCount.textContent = count;
            cartCount.style.display = count > 0 ? 'flex' : 'none';
        }

        document.querySelectorAll(".remove-item").forEach(button => {
            button.addEventListener("click", e => {
                const id = e.target.dataset.id;
                removeFromCartServer(id);
            });
        });
    }

    // Listener para botões "Adicionar ao Carrinho"
    // Aceita tanto .product-card quanto .produto-card
    document.body.addEventListener("click", (e) => {
        if (e.target.classList.contains("add-to-cart")) {
            e.preventDefault();
            const card = e.target.closest(".product-card, .produto-card");
            if (!card) {
                console.error("Card de produto não encontrado");
                return;
            }
            const product = {
                produto_id: card.dataset.id,
                nome: card.dataset.name,
                preco: parseFloat(card.dataset.price),
                quantidade: 1
            };
            addToCartServer(product);
        }
    });

    // Fechar carrinho ao clicar fora
    document.addEventListener("click", (e) => {
        if (cartOverlay && cartOverlay.classList.contains("open") &&
            !cartOverlay.contains(e.target) &&
            e.target !== cartToggle) {
            cartOverlay.classList.remove("open");
        }
    });

    if (checkoutBtn) {
        checkoutBtn.addEventListener("click", () => {
            checkLogin().then(isLoggedIn => {
                window.location.href = isLoggedIn ? 'checkout.php' : 'login.php?redirect=checkout.php';
            });
        });
    }

    // Toast helper
    function showToast(message, isError = false) {
        let toast = document.getElementById('toast-msg');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast-msg';
            toast.style.cssText = 'position:fixed; bottom:30px; right:30px; padding:14px 20px; border-radius:6px; color:white; font-weight:500; z-index:9999; box-shadow:0 4px 12px rgba(0,0,0,0.2); transition:opacity 0.3s; opacity:0;';
            document.body.appendChild(toast);
        }
        toast.style.background = isError ? '#e74c3c' : '#4CAF50';
        toast.textContent = message;
        toast.style.opacity = '1';
        clearTimeout(toast._hideTimer);
        toast._hideTimer = setTimeout(() => {
            toast.style.opacity = '0';
        }, 2500);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Carregar carrinho ao iniciar
    updateCartFromServer();
});
