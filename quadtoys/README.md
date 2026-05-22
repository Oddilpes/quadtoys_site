# QuadToys — Site de Vendas de Colecionáveis

Projeto acadêmico de e-commerce em PHP + MySQL.

## 📁 Estrutura

```
quadtoys/
├── includes/
│   ├── config.php       ← Conexão com o banco (ÚNICO arquivo a editar para deploy)
│   ├── header.php       ← Cabeçalho HTML + nav
│   └── footer.php       ← Rodapé HTML + carrinho overlay
├── images/
│   ├── placeholder.jpg  ← Imagem padrão quando falta foto
│   └── produtos/
│       └── produto_<ID>.jpg   ← Coloque aqui as fotos (ex: produto_1.jpg)
├── index.php            ← Página inicial
├── categorias.php       ← Lista de categorias
├── colecoes.php         ← Coleções por categoria
├── destaques.php        ← Produtos em destaque
├── produtos_por_categoria.php  ← Produtos de uma categoria
├── produto.php          ← Detalhes de um produto
├── carrinho.php         ← Carrinho completo
├── checkout.php         ← Finalização da compra
├── comunidade.php       ← Página de comunidade
├── contato.php          ← Página de contato
├── login.php / cadastro.php / logout.php
├── add_to_cart.php / get_cart.php / remove_from_cart.php  ← AJAX
├── check_login.php      ← AJAX
├── script.js            ← JS do carrinho
├── style.css            ← Estilos
└── colecionaveis_setup.sql   ← Banco de dados (importe no phpMyAdmin)
```

## 🚀 Instalação local (XAMPP)

1. Copie a pasta `quadtoys/` para `htdocs/` do XAMPP
2. Abra `phpMyAdmin` → crie um banco chamado `colecionaveis`
3. Importe o arquivo `colecionaveis_setup.sql`
4. Em `includes/config.php`, deixe `$AMBIENTE = 'local';` (já está assim por padrão)
5. Acesse `http://localhost/quadtoys/`

**Login de teste:** `teste@quadtoys.com` / senha `teste123`
(ou crie uma conta nova pela página de cadastro)

## 🌐 Deploy no InfinityFree

### Passo 1 — Criar conta e site
1. Crie conta gratuita em https://infinityfree.com
2. No Client Area, clique em **"New Account"** e siga o assistente para criar um subdomínio (ex: `quadtoys.infinityfreeapp.com`)
3. Aguarde alguns minutos até a propagação

### Passo 2 — Criar banco de dados
1. Client Area → seu site → **"MySQL Databases"**
2. Clique em **"Create Database"** e dê um nome (ex: `colecionaveis`)
3. **Anote essas informações** — você vai precisar:
   - **MySQL Hostname** (algo como `sql100.infinityfree.com`)
   - **MySQL Database name** (algo como `if0_12345678_colecionaveis`)
   - **MySQL Username** (algo como `if0_12345678`)
   - **MySQL Password** (a que você definiu)
4. Na linha do banco recém-criado, clique em **"Admin"** para abrir o phpMyAdmin
5. Aba **"Importar"** → selecione `colecionaveis_setup.sql` → **Executar**

### Passo 3 — Editar config.php

Abra `includes/config.php` e:
1. Mude `$AMBIENTE = 'local';` para `$AMBIENTE = 'producao';`
2. Substitua os 4 valores no bloco "AMBIENTE INFINITYFREE" pelos dados que você anotou:

```php
$db_host = 'sql100.infinityfree.com';       // seu MySQL Hostname
$db_name = 'if0_12345678_colecionaveis';    // seu Database name
$db_user = 'if0_12345678';                  // seu Username
$db_pass = 'SuaSenhaAqui';                  // sua Password
```

### Passo 4 — Upload dos arquivos
1. Client Area → seu site → **"File Manager"** (ou use FTP/FileZilla com as credenciais que aparecem em "FTP Accounts")
2. Entre na pasta **`htdocs/`** (NÃO faça upload na raiz!)
3. Apague o `index2.html` que vem por padrão
4. Faça upload de TODOS os arquivos do projeto (PHP, CSS, JS, pasta `includes/`, pasta `images/`)

### Passo 5 — Testar
1. Acesse seu domínio (ex: `https://quadtoys.infinityfreeapp.com`)
2. Se a página inicial carregar com produtos, deu certo!
3. Teste login com `teste@quadtoys.com` / `teste123`
4. Teste adicionar produtos ao carrinho e finalizar compra

## 🐛 Problemas comuns

| Problema | Solução |
|----------|---------|
| "Erro interno" ao acessar | Confira as 4 credenciais no `config.php` |
| Produtos não aparecem | Verifique se importou o `colecionaveis_setup.sql` |
| "Página não encontrada" | Confirme que os arquivos estão em `htdocs/` |
| Carrinho não funciona | Abra o console do navegador (F12) e procure erros |
| Imagens não aparecem | Coloque fotos em `images/produtos/` com nome `produto_<ID>.jpg` (ex: `produto_1.jpg` para o produto de id 1). Se faltar, mostra placeholder. |
| InfinityFree mostra "page not found" estranho | Pode ser bloqueio temporário do CDN deles — limpe cache (Ctrl+F5) e tente em janela anônima |
| Login não aceita usuários antigos do SQL original | As senhas no SQL original eram texto puro ("hash_senha_123"). Use `teste@quadtoys.com`/`teste123` ou cadastre um usuário novo |

## 🖼️ Adicionar imagens aos produtos

As páginas procuram imagens em `images/produtos/produto_<ID>.jpg`. Por exemplo:
- Produto id 1 (Funko Iron Man) → `images/produtos/produto_1.jpg`
- Produto id 2 (Card Charizard) → `images/produtos/produto_2.jpg`
- ...

Se não houver imagem, mostra um placeholder. Se quiser, depois você pode adicionar uma coluna `imagem` na tabela `produtos` e ajustar os SELECTs — mas como está, já funciona.

## ✅ Funcionalidades implementadas

- ✅ Listagem de produtos por categoria
- ✅ Página de detalhes do produto (com avaliações)
- ✅ Cadastro/Login/Logout de clientes
- ✅ Carrinho de compras (sidebar e página dedicada)
- ✅ Checkout funcional (cria pedido real + dá baixa no estoque)
- ✅ Páginas institucionais (comunidade, contato)
- ✅ Responsivo (funciona em celular)
- ✅ Avaliações de produtos

## ⚠️ Limitações (para um projeto acadêmico)

- Pagamento é simulado (não integra com gateway real)
- Newsletter e formulário de contato são apenas demo (não enviam de fato)
- Imagens dos produtos precisam ser adicionadas manualmente
- O InfinityFree free tier não permite SMTP, então mesmo se fosse implementar e-mail, não funcionaria sem upgrade
