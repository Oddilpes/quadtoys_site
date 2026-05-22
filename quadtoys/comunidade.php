<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="comunidade-container">
        <div class="page-header">
            <h1>Comunidade QuadToys</h1>
            <p>Conecte-se com outros colecionadores, participe de eventos e fique por dentro das novidades!</p>
        </div>

        <div class="comunidades-grid">
            <div class="comunidade-card">
                <div class="comunidade-img" style="background:linear-gradient(135deg,#667eea,#764ba2);"></div>
                <div class="comunidade-info">
                    <h3>QuadMania</h3>
                    <p>O maior grupo de colecionadores de quadrinhos do Brasil. Troque, venda e compre itens raros.</p>
                    <p class="comunidade-membros">2.500+ membros</p>
                    <a href="https://facebook.com/groups/quadmania" target="_blank" class="btn-join">Participar</a>
                </div>
            </div>

            <div class="comunidade-card">
                <div class="comunidade-img" style="background:linear-gradient(135deg,#f093fb,#f5576c);"></div>
                <div class="comunidade-info">
                    <h3>Action Collectors</h3>
                    <p>Comunidade especializada em action figures de todos os universos. Compartilhe sua coleção!</p>
                    <p class="comunidade-membros">1.800+ membros</p>
                    <a href="#" class="btn-join">Entrar no Discord</a>
                </div>
            </div>

            <div class="comunidade-card">
                <div class="comunidade-img" style="background:linear-gradient(135deg,#4facfe,#00f2fe);"></div>
                <div class="comunidade-info">
                    <h3>Tokusatsu Brasil</h3>
                    <p>Dedicado aos fãs de séries japonesas como Ultraman, Kamen Rider e Super Sentai.</p>
                    <p class="comunidade-membros">1.200+ membros</p>
                    <a href="#" class="btn-join">Entrar no Telegram</a>
                </div>
            </div>

            <div class="comunidade-card">
                <div class="comunidade-img" style="background:linear-gradient(135deg,#fa709a,#fee140);"></div>
                <div class="comunidade-info">
                    <h3>QuadToys Clube</h3>
                    <p>Grupo oficial da QuadToys. Seja o primeiro a saber sobre novidades, promoções exclusivas e eventos.</p>
                    <p class="comunidade-membros">3.200+ membros</p>
                    <a href="#" class="btn-join">Seguir no Instagram</a>
                </div>
            </div>
        </div>

        <div class="eventos-section">
            <h2>Próximos Eventos</h2>

            <div class="eventos-list">
                <div class="evento-card">
                    <div class="evento-data">
                        <span class="dia">15</span>
                        <span class="mes">Jun</span>
                    </div>
                    <div class="evento-info">
                        <h3>Encontro de Colecionadores QuadToys</h3>
                        <p class="evento-local">Shopping Center Norte - São Paulo, SP</p>
                        <p>O maior encontro de colecionadores do Brasil!</p>
                        <a href="#" class="btn-evento">Saiba mais</a>
                    </div>
                </div>

                <div class="evento-card">
                    <div class="evento-data">
                        <span class="dia">22</span>
                        <span class="mes">Jul</span>
                    </div>
                    <div class="evento-info">
                        <h3>Comic-Con QuadToys</h3>
                        <p class="evento-local">Expo Center Norte - São Paulo, SP</p>
                        <p>Evento especial com lançamentos exclusivos e sessões de autógrafos.</p>
                        <a href="#" class="btn-evento">Saiba mais</a>
                    </div>
                </div>

                <div class="evento-card">
                    <div class="evento-data">
                        <span class="dia">10</span>
                        <span class="mes">Ago</span>
                    </div>
                    <div class="evento-info">
                        <h3>Workshop: Conservação de Quadrinhos</h3>
                        <p class="evento-local">Online - Transmissão ao vivo</p>
                        <p>Técnicas profissionais para preservar suas revistas em quadrinhos.</p>
                        <a href="#" class="btn-evento">Inscreva-se</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .comunidade-container { padding: 40px 0; }
    .page-header { text-align: center; margin-bottom: 50px; }
    .page-header h1 { font-size: 2.5em; color: #2c3e50; margin-bottom: 15px; }
    .page-header p { font-size: 1.1em; color: #666; max-width: 700px; margin: 0 auto; }

    .comunidades-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 25px;
        margin-bottom: 60px;
    }
    .comunidade-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.3s;
    }
    .comunidade-card:hover { transform: translateY(-5px); }
    .comunidade-img { height: 140px; }
    .comunidade-info { padding: 20px; }
    .comunidade-info h3 { font-size: 1.3em; color: #2c3e50; margin-bottom: 10px; }
    .comunidade-info p { color: #666; margin-bottom: 12px; line-height: 1.5; }
    .comunidade-membros { color: #4CAF50 !important; font-weight: bold; font-size: 0.9em; }
    .btn-join {
        display: inline-block;
        background-color: #4CAF50;
        color: white;
        padding: 8px 20px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s;
    }
    .btn-join:hover { background-color: #45a049; }

    .eventos-section { margin-bottom: 40px; }
    .eventos-section h2 {
        font-size: 1.8em;
        color: #2c3e50;
        margin-bottom: 25px;
        text-align: center;
    }
    .eventos-list { display: flex; flex-direction: column; gap: 18px; }
    .evento-card {
        display: flex;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .evento-data {
        background-color: #4CAF50;
        color: white;
        padding: 15px;
        min-width: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .evento-data .dia { font-size: 1.8em; font-weight: bold; }
    .evento-data .mes { font-size: 0.9em; text-transform: uppercase; }
    .evento-info { padding: 18px; flex-grow: 1; }
    .evento-info h3 { font-size: 1.2em; color: #2c3e50; margin-bottom: 5px; }
    .evento-local { color: #777; font-style: italic; font-size: 0.9em; margin-bottom: 8px; }
    .btn-evento {
        display: inline-block;
        background-color: #ecf0f1;
        color: #2c3e50;
        padding: 6px 14px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        font-size: 0.9em;
        margin-top: 8px;
    }
    .btn-evento:hover { background-color: #bdc3c7; }
</style>

<?php include 'includes/footer.php'; ?>
