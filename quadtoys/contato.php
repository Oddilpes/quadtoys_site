<?php include 'includes/header.php'; ?>

<div class="container" style="padding: 40px 0;">
    <div class="contact-container">
        <h1>Entre em Contato</h1>
        <p class="contact-intro">Estamos aqui para ajudar! Entre em contato pelos canais abaixo:</p>

        <div class="contact-grid">
            <div class="contact-card">
                <div class="contact-icon">📧</div>
                <h3>E-mail</h3>
                <a href="mailto:contato@quadtoys.com.br">contato@quadtoys.com.br</a>
            </div>

            <div class="contact-card">
                <div class="contact-icon">📞</div>
                <h3>Telefone</h3>
                <a href="tel:+551935784125">(19) 3578-4125</a>
            </div>

            <div class="contact-card">
                <div class="contact-icon">💬</div>
                <h3>WhatsApp</h3>
                <a href="https://wa.me/5519982647391" target="_blank">(19) 9 8264-7391</a>
            </div>

            <div class="contact-card">
                <div class="contact-icon">📍</div>
                <h3>Endereço</h3>
                <p>Rua quatorze de dezembro, 80, Apto 67<br>
                Centro, Campinas - SP<br>
                CEP 13015-130</p>
            </div>
        </div>

        <div class="form-contato">
            <h2>Envie uma mensagem</h2>
            <form onsubmit="alert('Mensagem enviada! (demo)'); return false;">
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" required class="form-control">
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Mensagem</label>
                    <textarea name="mensagem" rows="5" required class="form-control"></textarea>
                </div>
                <button type="submit" class="btn">Enviar Mensagem</button>
            </form>
        </div>
    </div>
</div>

<style>
    .contact-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 30px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .contact-container h1 {
        color: #2c3e50;
        margin-bottom: 10px;
        text-align: center;
    }
    .contact-intro {
        text-align: center;
        color: #666;
        margin-bottom: 30px;
    }
    .contact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }
    .contact-card {
        text-align: center;
        padding: 25px 15px;
        background: #f9f9f9;
        border-radius: 8px;
        transition: transform 0.2s;
    }
    .contact-card:hover { transform: translateY(-5px); }
    .contact-icon { font-size: 2.5em; margin-bottom: 10px; }
    .contact-card h3 { color: #2c3e50; margin-bottom: 8px; }
    .contact-card a { color: #e74c3c; text-decoration: none; }
    .contact-card a:hover { text-decoration: underline; }
    .contact-card p { color: #555; font-size: 0.9em; line-height: 1.5; }

    .form-contato {
        max-width: 600px;
        margin: 0 auto;
        padding-top: 30px;
        border-top: 1px solid #eee;
    }
    .form-contato h2 {
        color: #2c3e50;
        margin-bottom: 20px;
        text-align: center;
    }
    .form-group { margin-bottom: 18px; }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: #555;
        font-weight: 500;
    }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1em;
        font-family: inherit;
    }
    .form-control:focus {
        outline: none;
        border-color: #4CAF50;
    }
</style>

<?php include 'includes/footer.php'; ?>
