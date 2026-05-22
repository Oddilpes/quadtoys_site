-- ===========================================================
-- QuadToys - Schema e dados de teste
-- ===========================================================
-- Importe este arquivo no phpMyAdmin (local ou InfinityFree)
-- Para o InfinityFree:
--   1. Crie a database no painel (Client Area > MySQL Databases)
--   2. Abra o phpMyAdmin que aparece para essa database
--   3. Aba "Importar" > selecione este arquivo > Executar
-- ===========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ===========================================================
-- TABELAS
-- ===========================================================

DROP TABLE IF EXISTS `transacoes`;
DROP TABLE IF EXISTS `pagamentos`;
DROP TABLE IF EXISTS `itens_pedido`;
DROP TABLE IF EXISTS `avaliacoes`;
DROP TABLE IF EXISTS `pedidos`;
DROP TABLE IF EXISTS `lista_desejos`;
DROP TABLE IF EXISTS `carrinho_compras`;
DROP TABLE IF EXISTS `enderecos`;
DROP TABLE IF EXISTS `cupons`;
DROP TABLE IF EXISTS `promocoes`;
DROP TABLE IF EXISTS `produtos`;
DROP TABLE IF EXISTS `categorias`;
DROP TABLE IF EXISTS `clientes`;
DROP TABLE IF EXISTS `fornecedores`;

CREATE TABLE `clientes` (
  `cliente_id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `data_cadastro` datetime DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'ativo',
  PRIMARY KEY (`cliente_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `cpf` (`cpf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categorias` (
  `categoria_id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `categoria_pai_id` int(11) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`categoria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `produtos` (
  `produto_id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `estoque` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `data_cadastro` date DEFAULT NULL,
  `peso` decimal(6,3) DEFAULT NULL,
  `dimensoes` varchar(50) DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`produto_id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`categoria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `carrinho_compras` (
  `carrinho_id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data_adicao` datetime NOT NULL,
  PRIMARY KEY (`carrinho_id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `carrinho_compras_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`) ON DELETE CASCADE,
  CONSTRAINT `carrinho_compras_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `enderecos` (
  `endereco_id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `cep` varchar(9) NOT NULL,
  `logradouro` varchar(100) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(50) NOT NULL,
  `cidade` varchar(50) NOT NULL,
  `estado` char(2) NOT NULL,
  `padrao` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`endereco_id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `enderecos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pedidos` (
  `pedido_id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `data_pedido` datetime NOT NULL,
  `status_pedido` varchar(30) NOT NULL,
  `endereco_entrega_id` int(11) DEFAULT NULL,
  `valor_produtos` decimal(10,2) NOT NULL,
  `valor_frete` decimal(10,2) NOT NULL,
  `valor_desconto` decimal(10,2) DEFAULT 0.00,
  `valor_total` decimal(10,2) NOT NULL,
  `codigo_rastreio` varchar(50) DEFAULT NULL,
  `metodo_pagamento` varchar(30) DEFAULT NULL,
  `cupom_id` int(11) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  PRIMARY KEY (`pedido_id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `endereco_entrega_id` (`endereco_entrega_id`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`),
  CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`endereco_entrega_id`) REFERENCES `enderecos` (`endereco_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `itens_pedido` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`pedido_id`),
  CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `avaliacoes` (
  `avaliacao_id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `nota` int(11) NOT NULL CHECK (`nota` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `data_avaliacao` datetime NOT NULL,
  `aprovado` tinyint(1) DEFAULT 0,
  `motivo_rejeicao` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`avaliacao_id`),
  KEY `produto_id` (`produto_id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `pedido_id` (`pedido_id`),
  CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`produto_id`),
  CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`),
  CONSTRAINT `avaliacoes_ibfk_3` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`pedido_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===========================================================
-- DADOS
-- ===========================================================

-- Categorias
INSERT INTO `categorias` (`categoria_id`, `nome`, `descricao`, `categoria_pai_id`, `ativo`) VALUES
(1, 'Action Figures', 'Figuras colecionáveis de personagens', NULL, 1),
(2, 'Cards Colecionáveis', 'Cartas de jogos colecionáveis', NULL, 1),
(3, 'Miniaturas', 'Réplicas em escala reduzida', NULL, 1),
(4, 'Quadrinhos', 'HQs e mangás colecionáveis', NULL, 1),
(5, 'Funko Pop', 'Bonecos estilizados da marca Funko', 1, 1),
(6, 'Marvel', 'Colecionáveis do universo Marvel', NULL, 1),
(7, 'DC Comics', 'Colecionáveis do universo DC', NULL, 1),
(8, 'Anime', 'Colecionáveis de animes japoneses', NULL, 1),
(9, 'Star Wars', 'Colecionáveis da saga Star Wars', NULL, 1),
(10, 'Harry Potter', 'Colecionáveis do universo Harry Potter', NULL, 1);

-- Produtos
INSERT INTO `produtos` (`produto_id`, `nome`, `descricao`, `preco`, `estoque`, `categoria_id`, `data_cadastro`, `peso`, `dimensoes`, `destaque`) VALUES
(1, 'Funko Pop Iron Man', 'Funko Pop do personagem Iron Man do universo Marvel. Modelo exclusivo com armadura Mark 50.', 129.90, 15, 5, '2024-01-15', 0.120, '10x10x15cm', 1),
(2, 'Card Charizard Raro', 'Carta rara do Pokémon Charizard, edição limitada com holografia. Item certificado.', 759.90, 3, 2, '2024-01-20', 0.010, '6.3x8.8cm', 1),
(3, 'Action Figure Spider-Man', 'Action Figure articulada do Homem-Aranha com mais de 30 pontos de articulação. Inclui acessórios.', 349.90, 8, 1, '2024-02-01', 0.350, '25x10x5cm', 0),
(4, 'Miniatura Batmóvel 1989', 'Réplica do Batmóvel do filme Batman de 1989. Escala 1/24, detalhes minuciosos.', 289.90, 5, 3, '2024-02-10', 0.500, '20x8x6cm', 1),
(5, 'Mangá One Piece Vol.1 Capa Dura', 'Edição colecionador de One Piece volume 1 com capa dura. Inclui artes exclusivas.', 89.90, 20, 4, '2024-02-15', 0.400, '21x14x2cm', 0),
(6, 'Varinha Harry Potter', 'Réplica oficial da varinha do Harry Potter. Madeira maciça com acabamento detalhado.', 199.90, 12, 10, '2024-03-01', 0.200, '35x3x3cm', 0),
(7, 'Action Figure Darth Vader', 'Figure do Darth Vader em escala 1/6 com pano e sabre de luz iluminado.', 899.90, 2, 9, '2024-03-10', 1.200, '30x20x15cm', 1),
(8, 'Card Magic Black Lotus', 'Réplica oficial da famosa carta Black Lotus de Magic the Gathering.', 129.90, 7, 2, '2024-03-15', 0.010, '6.3x8.8cm', 0),
(9, 'Funko Pop Goku', 'Funko Pop do personagem Goku em modo Super Sayajin do anime Dragon Ball.', 119.90, 10, 5, '2024-03-20', 0.120, '10x10x15cm', 0),
(10, 'HQ Batman: O Cavaleiro das Trevas', 'Edição de luxo encadernada da clássica HQ de Frank Miller. Capa dura.', 159.90, 6, 4, '2024-03-25', 0.800, '28x20x3cm', 1);

-- ===========================================================
-- USUÁRIO DE TESTE
-- ===========================================================
-- Email: teste@quadtoys.com
-- Senha: teste123
-- (Hash bcrypt válido — funciona com password_verify do PHP)
INSERT INTO `clientes` (`cliente_id`, `nome`, `email`, `senha`, `data_cadastro`, `ultimo_acesso`, `status`) VALUES
(1, 'Usuario Teste', 'teste@quadtoys.com', '$2b$10$/lP1dMQUgRBmFYfSGWRB6uKGzWefzy8iP919YVV/Pf/GO/T7vRcoC', NOW(), NOW(), 'ativo');

-- Algumas avaliações de exemplo
INSERT INTO `avaliacoes` (`produto_id`, `cliente_id`, `nota`, `comentario`, `data_avaliacao`, `aprovado`) VALUES
(1, 1, 5, 'Excelente produto! Detalhes impecáveis e embalagem caprichada.', NOW(), 1),
(2, 1, 4, 'Carta chegou bem protegida, holografia linda.', NOW(), 1),
(3, 1, 5, 'Action Figure com ótimo acabamento. Recomendo!', NOW(), 1);

COMMIT;
