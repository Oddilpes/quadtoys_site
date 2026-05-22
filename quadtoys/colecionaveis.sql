-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 11/03/2025 às 12:27
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `colecionaveis`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `avaliacao_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `nota` int(11) NOT NULL CHECK (`nota` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `data_avaliacao` datetime NOT NULL,
  `aprovado` tinyint(1) DEFAULT 0,
  `motivo_rejeicao` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `avaliacoes`
--

INSERT INTO `avaliacoes` (`avaliacao_id`, `produto_id`, `cliente_id`, `pedido_id`, `nota`, `comentario`, `data_avaliacao`, `aprovado`, `motivo_rejeicao`) VALUES
(1, 1, 1, 1, 5, 'Excelente produto! Superou minhas expectativas.', '2024-01-20 15:30:22', 1, NULL),
(2, 2, 2, 2, 4, 'Bom produto, entrega rápida.', '2024-01-25 10:15:30', 1, NULL),
(3, 3, 3, 3, 5, 'Action Figure com ótimo acabamento e detalhes perfeitos.', '2024-02-01 14:20:15', 1, NULL),
(4, 4, 1, 4, 3, 'Produto bom, mas veio com uma pequena arranhão na parte traseira.', '2024-02-06 16:45:50', 1, NULL),
(5, 5, 4, NULL, 5, 'Edição muito bonita, papel de qualidade.', '2024-02-10 09:10:25', 1, NULL),
(6, 6, 6, 6, 1, 'Produto quebrado, péssima embalagem.', '2024-02-15 12:30:18', 0, 'Linguagem inapropriada'),
(7, 1, 7, 7, 4, 'Bom custo-benefício, recomendo.', '2024-02-15 19:25:40', 1, NULL),
(8, 9, 7, 7, 5, 'Funko perfeito, idêntico ao personagem!', '2024-02-15 19:30:12', 1, NULL),
(9, 9, 9, 8, 4, 'Gostei muito, chegou bem embalado.', '2024-02-17 21:15:30', 1, NULL),
(10, 10, 10, NULL, 2, 'Qualidade abaixo do esperado para o preço cobrado.', '2024-02-19 08:45:22', 1, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho_compras`
--

CREATE TABLE `carrinho_compras` (
  `carrinho_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data_adicao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `carrinho_compras`
--

INSERT INTO `carrinho_compras` (`carrinho_id`, `cliente_id`, `produto_id`, `quantidade`, `data_adicao`) VALUES
(1, 1, 7, 1, '2024-02-18 14:30:22'),
(2, 1, 5, 2, '2024-02-18 14:32:15'),
(3, 3, 2, 1, '2024-02-17 19:45:10'),
(4, 5, 1, 1, '2024-02-16 09:20:05'),
(5, 5, 10, 1, '2024-02-16 09:22:30'),
(6, 6, 9, 1, '2024-02-15 16:10:45'),
(7, 7, 6, 1, '2024-02-18 18:05:12'),
(8, 8, 3, 1, '2024-02-14 20:15:33'),
(9, 9, 4, 1, '2024-02-18 11:30:25'),
(10, 10, 8, 1, '2024-02-17 13:40:18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `categoria_id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `categoria_pai_id` int(11) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

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

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `cliente_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `data_cadastro` datetime DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`cliente_id`, `nome`, `email`, `senha`, `cpf`, `telefone`, `data_nascimento`, `data_cadastro`, `ultimo_acesso`, `status`) VALUES
(1, 'João Silva', 'joao.silva@email.com', 'hash_senha_123', '123.456.789-00', '(11) 91234-5678', '1990-05-15', '2023-10-01 14:30:00', '2024-02-15 08:45:12', 'ativo'),
(2, 'Maria Oliveira', 'maria.o@email.com', 'hash_senha_456', '987.654.321-00', '(11) 99876-5432', '1985-08-22', '2023-10-15 09:20:15', '2024-02-14 19:22:45', 'ativo'),
(3, 'Pedro Santos', 'pedro.s@email.com', 'hash_senha_789', '456.789.123-00', '(21) 98765-4321', '1992-03-10', '2023-11-05 16:45:30', '2024-02-10 12:10:05', 'ativo'),
(4, 'Ana Costa', 'ana.costa@email.com', 'hash_senha_101', '789.123.456-00', '(47) 99123-4567', '1988-12-05', '2023-11-20 11:15:22', '2024-02-12 17:30:18', 'ativo'),
(5, 'Lucas Mendes', 'lucas.m@email.com', 'hash_senha_202', '321.654.987-00', '(31) 98234-5678', '1995-07-18', '2023-12-03 08:50:10', '2024-01-25 20:15:33', 'inativo'),
(6, 'Juliana Ferreira', 'ju.ferreira@email.com', 'hash_senha_303', '654.987.321-00', '(19) 91876-5432', '1987-09-30', '2023-12-10 13:25:45', '2024-02-18 09:50:20', 'ativo'),
(7, 'Rodrigo Almeida', 'rodrigo.a@email.com', 'hash_senha_404', '234.567.890-00', '(41) 99567-8901', '1993-04-25', '2024-01-05 15:10:18', '2024-02-01 14:20:44', 'ativo'),
(8, 'Carla Souza', 'carla.s@email.com', 'hash_senha_505', '567.890.123-00', '(51) 98765-0123', '1991-11-12', '2024-01-15 10:05:32', '2024-01-16 11:22:37', 'suspenso'),
(9, 'Marcos Pereira', 'marcos.p@email.com', 'hash_senha_606', '890.123.456-00', '(27) 99432-1098', '1984-02-28', '2024-01-22 17:30:55', '2024-02-17 16:40:12', 'ativo'),
(10, 'Fernanda Lima', 'fer.lima@email.com', 'hash_senha_707', '432.765.098-00', '(85) 98123-6549', '1996-06-14', '2024-02-01 09:15:40', '2024-02-17 18:55:30', 'ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cupons`
--

CREATE TABLE `cupons` (
  `cupom_id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `promocao_id` int(11) DEFAULT NULL,
  `tipo` varchar(30) NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_validade` date NOT NULL,
  `limite_usos` int(11) DEFAULT NULL,
  `usos_realizados` int(11) DEFAULT 0,
  `valor_minimo_pedido` decimal(10,2) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cupons`
--

INSERT INTO `cupons` (`cupom_id`, `codigo`, `promocao_id`, `tipo`, `valor`, `data_validade`, `limite_usos`, `usos_realizados`, `valor_minimo_pedido`, `ativo`) VALUES
(1, 'BEMVINDO20', NULL, 'percentual', 20.00, '2024-12-31', 1, 1, 100.00, 1),
(2, 'ANIVERSARIO10', NULL, 'percentual', 10.00, '2024-12-31', 1, 1, 50.00, 1),
(3, 'BLACKFRIDAY15', 1, 'percentual', 15.00, '2023-11-26', 1, 1, 200.00, 0),
(4, 'FRETEGRATIS', NULL, 'frete_gratis', NULL, '2024-06-30', 1, 0, 150.00, 1),
(5, 'DESC10MARVEL', 8, 'percentual', 10.00, '2024-04-07', 1, 1, 80.00, 1),
(6, 'VOLTA50', NULL, 'valor_fixo', 50.00, '2024-03-31', 1, 0, 200.00, 1),
(7, 'APP15OFF', NULL, 'percentual', 15.00, '2024-04-30', 1, 0, 120.00, 1),
(8, 'NEWUSER25', NULL, 'percentual', 25.00, '2024-12-31', 1, 0, 100.00, 1),
(9, 'COLLECTOR10', 10, 'percentual', 10.00, '2024-05-10', 1, 0, 80.00, 1),
(10, 'FIXO30REAIS', NULL, 'valor_fixo', 30.00, '2024-06-30', 1, 0, 150.00, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `endereco_id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `cep` varchar(9) NOT NULL,
  `logradouro` varchar(100) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(50) NOT NULL,
  `cidade` varchar(50) NOT NULL,
  `estado` char(2) NOT NULL,
  `padrao` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `enderecos`
--

INSERT INTO `enderecos` (`endereco_id`, `cliente_id`, `tipo`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `padrao`) VALUES
(1, 1, 'entrega', '01001-000', 'Rua Augusta', '1200', 'Apto 45', 'Consolação', 'São Paulo', 'SP', 1),
(2, 1, 'cobrança', '01001-000', 'Rua Augusta', '1200', 'Apto 45', 'Consolação', 'São Paulo', 'SP', 0),
(3, 2, 'entrega', '22041-001', 'Av. Atlântica', '500', 'Bloco B, Apto 102', 'Copacabana', 'Rio de Janeiro', 'RJ', 1),
(4, 3, 'entrega', '80010-010', 'Rua XV de Novembro', '123', NULL, 'Centro', 'Curitiba', 'PR', 1),
(5, 4, 'entrega', '30170-110', 'Av. do Contorno', '789', 'Casa 2', 'Funcionários', 'Belo Horizonte', 'MG', 1),
(6, 5, 'entrega', '40026-280', 'Av. Sete de Setembro', '1500', 'Sala 304', 'Campo Grande', 'Salvador', 'BA', 1),
(7, 6, 'entrega', '69067-001', 'Av. André Araújo', '85', NULL, 'Aleixo', 'Manaus', 'AM', 1),
(8, 7, 'entrega', '90619-900', 'Av. Ipiranga', '6681', 'Bloco 8', 'Partenon', 'Porto Alegre', 'RS', 1),
(9, 8, 'entrega', '50030-150', 'Rua da Aurora', '325', 'Apto 1201', 'Boa Vista', 'Recife', 'PE', 1),
(10, 9, 'entrega', '70200-010', 'Eixo Monumental', '100', 'Quadra 5, Lote 3', 'Zona Cívica', 'Brasília', 'DF', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedores`
--

CREATE TABLE `fornecedores` (
  `fornecedor_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cnpj` varchar(18) DEFAULT NULL,
  `contato_nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `categorias_fornecidas` varchar(200) DEFAULT NULL,
  `prazo_entrega_dias` int(11) DEFAULT NULL,
  `valor_minimo_pedido` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fornecedores`
--

INSERT INTO `fornecedores` (`fornecedor_id`, `nome`, `cnpj`, `contato_nome`, `email`, `telefone`, `endereco`, `categorias_fornecidas`, `prazo_entrega_dias`, `valor_minimo_pedido`, `status`) VALUES
(1, 'Funko Brasil Ltda', '12.345.678/0001-90', 'Roberto Alves', 'contato@funkobrasil.com.br', '(11) 3456-7890', 'Rua das Figuras, 123 - São Paulo, SP', '1,5', 15, 5000.00, 'ativo'),
(2, 'Cards Collection Importadora', '23.456.789/0001-01', 'Mariana Silva', 'contato@cardscollection.com.br', '(21) 2345-6789', 'Av. dos Colecionáveis, 456 - Rio de Janeiro, RJ', '2', 20, 3000.00, 'ativo'),
(3, 'Action Toys Brasil', '34.567.890/0001-12', 'Carlos Mendes', 'compras@actiontoys.com.br', '(31) 3456-7891', 'Rua dos Brinquedos, 789 - Belo Horizonte, MG', '1,3', 10, 2500.00, 'ativo'),
(4, 'Mangá & Comics Distribuidora', '45.678.901/0001-23', 'Paula Oliveira', 'pedidos@mangacomics.com.br', '(11) 4567-8901', 'Rua dos Quadrinhos, 101 - São Paulo, SP', '4', 7, 1500.00, 'ativo'),
(5, 'Universo Geek Distribuição', '56.789.012/0001-34', 'Lucas Nunes', 'vendas@universogeek.com.br', '(41) 5678-9012', 'Av. Geek, 202 - Curitiba, PR', '1,2,3,4,5,6,7,8,9,10', 12, 4000.00, 'ativo'),
(6, 'Wizard Imports Ltda', '67.890.123/0001-45', 'Juliana Costa', 'contato@wizardimports.com.br', '(51) 6789-0123', 'Rua dos Magos, 303 - Porto Alegre, RS', '10', 30, 6000.00, 'inativo'),
(7, 'Galaxy Collectibles', '78.901.234/0001-56', 'Pedro Santos', 'vendas@galaxycollectibles.com.br', '(81) 7890-1234', 'Av. das Galáxias, 404 - Recife, PE', '8,9', 15, 3500.00, 'ativo'),
(8, 'Miniatures & Models Ltda', '89.012.345/0001-67', 'Ana Rodrigues', 'pedidos@miniaturesmodels.com.br', '(19) 8901-2345', 'Rua das Miniaturas, 505 - Campinas, SP', '3', 10, 2000.00, 'ativo'),
(9, 'Heroes Distribution Co.', '90.123.456/0001-78', 'Fábio Lima', 'contato@heroesdistribution.com.br', '(27) 9012-3456', 'Av. dos Heróis, 606 - Vitória, ES', '6,7', 14, 2800.00, 'ativo'),
(10, 'Japan Pop Culture Import', '01.234.567/0001-89', 'Yuri Tanaka', 'importacao@japanpopculture.com.br', '(11) 0123-4567', 'Rua Japão, 707 - São Paulo, SP', '8', 45, 8000.00, 'ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `item_id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens_pedido`
--

INSERT INTO `itens_pedido` (`item_id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`, `subtotal`) VALUES
(1, 1, 1, 1, 129.90, 129.90),
(2, 2, 2, 1, 759.90, 759.90),
(3, 3, 3, 1, 349.90, 349.90),
(4, 4, 4, 1, 289.90, 289.90),
(5, 5, 5, 1, 89.90, 89.90),
(6, 6, 6, 1, 199.90, 199.90),
(7, 7, 1, 1, 129.90, 129.90),
(8, 7, 9, 1, 119.90, 119.90),
(9, 7, 3, 2, 349.90, 699.80),
(10, 8, 9, 1, 119.90, 119.90),
(11, 8, 5, 1, 89.90, 89.90),
(12, 8, 8, 1, 129.90, 129.90),
(13, 9, 10, 1, 159.90, 159.90),
(14, 10, 3, 1, 349.90, 349.90);

-- --------------------------------------------------------

--
-- Estrutura para tabela `lista_desejos`
--

CREATE TABLE `lista_desejos` (
  `desejo_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `data_adicao` date NOT NULL,
  `notificar_promocao` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `lista_desejos`
--

INSERT INTO `lista_desejos` (`desejo_id`, `cliente_id`, `produto_id`, `data_adicao`, `notificar_promocao`) VALUES
(1, 1, 2, '2024-01-10', 1),
(2, 1, 7, '2024-01-15', 1),
(3, 2, 4, '2024-01-20', 0),
(4, 3, 6, '2024-01-25', 1),
(5, 4, 10, '2024-02-01', 0),
(6, 5, 3, '2024-02-05', 1),
(7, 6, 7, '2024-02-08', 1),
(8, 7, 5, '2024-02-10', 0),
(9, 8, 9, '2024-02-15', 1),
(10, 9, 1, '2024-02-18', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `pagamento_id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `status` varchar(30) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_pagamento` datetime DEFAULT NULL,
  `codigo_transacao` varchar(100) DEFAULT NULL,
  `parcelas` int(11) DEFAULT 1,
  `detalhes_pagamento` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `pedido_id` int(11) NOT NULL,
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
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`pedido_id`, `cliente_id`, `data_pedido`, `status_pedido`, `endereco_entrega_id`, `valor_produtos`, `valor_frete`, `valor_desconto`, `valor_total`, `codigo_rastreio`, `metodo_pagamento`, `cupom_id`, `observacoes`) VALUES
(1, 1, '2024-01-15 10:30:22', 'entregue', 1, 129.90, 15.50, 0.00, 145.40, 'BR123456789BR', 'cartão de crédito', NULL, NULL),
(2, 2, '2024-01-20 14:15:05', 'entregue', 3, 759.90, 20.00, 75.99, 703.91, 'BR987654321BR', 'pix', 3, NULL),
(3, 3, '2024-01-25 09:45:18', 'enviado', 4, 349.90, 18.50, 0.00, 368.40, 'BR456123789BR', 'boleto', NULL, 'Entregar somente ao destinatário'),
(4, 1, '2024-02-01 16:20:33', 'pago', 1, 289.90, 15.50, 29.00, 276.40, NULL, 'cartão de crédito', 5, NULL),
(5, 4, '2024-02-05 11:10:45', 'pendente', 5, 89.90, 12.00, 0.00, 101.90, NULL, 'aguardando pagamento', NULL, NULL),
(6, 6, '2024-02-10 15:30:12', 'cancelado', 7, 199.90, 25.90, 0.00, 225.80, NULL, 'cartão de débito', NULL, 'Cancelado a pedido do cliente'),
(7, 7, '2024-02-12 18:05:27', 'enviado', 8, 1019.80, 30.00, 102.00, 947.80, 'BR567890123BR', 'cartão de crédito', 2, NULL),
(8, 9, '2024-02-15 13:45:09', 'pago', 10, 249.80, 22.50, 0.00, 272.30, NULL, 'pix', NULL, NULL),
(9, 10, '2024-02-17 10:20:15', 'pendente', 10, 159.90, 18.90, 15.99, 162.81, NULL, 'aguardando pagamento', 4, NULL),
(10, 8, '2024-02-18 19:15:33', 'pago', 9, 349.90, 20.00, 70.00, 299.90, NULL, 'cartão de crédito', 1, 'Presente - embrulhar');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `produto_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `estoque` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `data_cadastro` date DEFAULT NULL,
  `peso` decimal(6,3) DEFAULT NULL,
  `dimensoes` varchar(50) DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`produto_id`, `nome`, `descricao`, `preco`, `estoque`, `categoria_id`, `data_cadastro`, `peso`, `dimensoes`, `destaque`) VALUES
(1, 'Funko Pop Iron Man', 'Funko Pop do personagem Iron Man', 129.90, 15, 5, '2024-01-15', 0.120, '10x10x15cm', 1),
(2, 'Card Charizard Raro', 'Carta rara do Pokémon Charizard', 759.90, 3, 2, '2024-01-20', 0.010, '6.3x8.8cm', 1),
(3, 'Action Figure Spider-Man', 'Action Figure articulada do Homem-Aranha', 349.90, 8, 1, '2024-02-01', 0.350, '25x10x5cm', 0),
(4, 'Miniatura Batmóvel 1989', 'Réplica do Batmóvel do filme de 1989', 289.90, 5, 3, '2024-02-10', 0.500, '20x8x6cm', 1),
(5, 'Mangá One Piece Vol.1 Capa Dura', 'Edição colecionador de One Piece', 89.90, 20, 4, '2024-02-15', 0.400, '21x14x2cm', 0),
(6, 'Varinha Harry Potter', 'Réplica da varinha do Harry Potter', 199.90, 12, 10, '2024-03-01', 0.200, '35x3x3cm', 0),
(7, 'Action Figure Darth Vader', 'Figure do Darth Vader em escala 1/6', 899.90, 2, 9, '2024-03-10', 1.200, '30x20x15cm', 1),
(8, 'Card Magic Black Lotus', 'Réplica da famosa carta Black Lotus', 129.90, 7, 2, '2024-03-15', 0.010, '6.3x8.8cm', 0),
(9, 'Funko Pop Goku', 'Funko Pop do personagem Goku', 119.90, 10, 5, '2024-03-20', 0.120, '10x10x15cm', 0),
(10, 'HQ Batman: O Cavaleiro das Trevas', 'Edição de luxo encadernada', 159.90, 6, 4, '2024-03-25', 0.800, '28x20x3cm', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `promocoes`
--

CREATE TABLE `promocoes` (
  `promocao_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` varchar(30) NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `limite_usos` int(11) DEFAULT NULL,
  `usos_realizados` int(11) DEFAULT 0,
  `status` varchar(20) DEFAULT 'ativa',
  `categorias_aplicaveis` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `promocoes`
--

INSERT INTO `promocoes` (`promocao_id`, `nome`, `descricao`, `tipo`, `valor`, `data_inicio`, `data_fim`, `limite_usos`, `usos_realizados`, `status`, `categorias_aplicaveis`) VALUES
(1, 'Black Friday', 'Descontos especiais de Black Friday', 'percentual', 20.00, '2023-11-24', '2023-11-26', 500, 487, 'expirada', '1,2,3,4,5'),
(2, 'Natal', 'Promoção de Natal', 'percentual', 10.00, '2023-12-15', '2023-12-25', 300, 256, 'expirada', '1,2,3,4,5,6,7,8,9,10'),
(3, 'Carnaval', 'Descontos de Carnaval', 'percentual', 15.00, '2024-02-10', '2024-02-14', 100, 78, 'expirada', '1,5,8'),
(4, 'Anime Week', 'Semana especial de produtos anime', 'percentual', 12.00, '2024-02-15', '2024-02-22', 200, 45, 'ativa', '8'),
(5, 'Frete Grátis SP', 'Frete grátis para SP em compras acima de R$200', 'frete_gratis', NULL, '2024-02-01', '2024-02-28', NULL, 124, 'ativa', NULL),
(6, 'Desconto Figures', 'Desconto especial em action figures', 'percentual', 8.00, '2024-03-01', '2024-03-15', 150, 0, 'inativa', '1'),
(7, 'Gift Cards', 'R$50 de desconto em gift cards', 'valor_fixo', 50.00, '2024-03-10', '2024-03-20', 50, 0, 'inativa', NULL),
(8, 'Marvel Week', 'Semana especial produtos Marvel', 'percentual', 17.00, '2024-04-01', '2024-04-07', 100, 0, 'inativa', '6'),
(9, 'DC Week', 'Semana especial produtos DC', 'percentual', 17.00, '2024-04-15', '2024-04-21', 100, 0, 'inativa', '7'),
(10, 'Dia do Colecionador', 'Desconto especial no dia do colecionador', 'percentual', 15.00, '2024-05-10', '2024-05-10', 200, 0, 'inativa', '1,2,3,4,5,6,7,8,9,10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `transacoes`
--

CREATE TABLE `transacoes` (
  `transacao_id` int(11) NOT NULL,
  `pagamento_id` int(11) DEFAULT NULL,
  `tipo` varchar(30) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_transacao` datetime NOT NULL,
  `status` varchar(30) NOT NULL,
  `gateway_pagamento` varchar(30) DEFAULT NULL,
  `codigo_autorizacao` varchar(50) DEFAULT NULL,
  `taxa_gateway` decimal(10,2) DEFAULT NULL,
  `detalhes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`avaliacao_id`),
  ADD KEY `produto_id` (`produto_id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Índices de tabela `carrinho_compras`
--
ALTER TABLE `carrinho_compras`
  ADD PRIMARY KEY (`carrinho_id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`categoria_id`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`cliente_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `cupons`
--
ALTER TABLE `cupons`
  ADD PRIMARY KEY (`cupom_id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `promocao_id` (`promocao_id`);

--
-- Índices de tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`endereco_id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Índices de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  ADD PRIMARY KEY (`fornecedor_id`),
  ADD UNIQUE KEY `cnpj` (`cnpj`);

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `lista_desejos`
--
ALTER TABLE `lista_desejos`
  ADD PRIMARY KEY (`desejo_id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`pagamento_id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`pedido_id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `endereco_entrega_id` (`endereco_entrega_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`produto_id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Índices de tabela `promocoes`
--
ALTER TABLE `promocoes`
  ADD PRIMARY KEY (`promocao_id`);

--
-- Índices de tabela `transacoes`
--
ALTER TABLE `transacoes`
  ADD PRIMARY KEY (`transacao_id`),
  ADD KEY `pagamento_id` (`pagamento_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `avaliacao_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`produto_id`),
  ADD CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`),
  ADD CONSTRAINT `avaliacoes_ibfk_3` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`pedido_id`);

--
-- Restrições para tabelas `carrinho_compras`
--
ALTER TABLE `carrinho_compras`
  ADD CONSTRAINT `carrinho_compras_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`),
  ADD CONSTRAINT `carrinho_compras_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`produto_id`);

--
-- Restrições para tabelas `cupons`
--
ALTER TABLE `cupons`
  ADD CONSTRAINT `cupons_ibfk_1` FOREIGN KEY (`promocao_id`) REFERENCES `promocoes` (`promocao_id`);

--
-- Restrições para tabelas `enderecos`
--
ALTER TABLE `enderecos`
  ADD CONSTRAINT `enderecos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`);

--
-- Restrições para tabelas `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`pedido_id`),
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`produto_id`);

--
-- Restrições para tabelas `lista_desejos`
--
ALTER TABLE `lista_desejos`
  ADD CONSTRAINT `lista_desejos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`),
  ADD CONSTRAINT `lista_desejos_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`produto_id`);

--
-- Restrições para tabelas `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`pedido_id`);

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`endereco_entrega_id`) REFERENCES `enderecos` (`endereco_id`);

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`categoria_id`);

--
-- Restrições para tabelas `transacoes`
--
ALTER TABLE `transacoes`
  ADD CONSTRAINT `transacoes_ibfk_1` FOREIGN KEY (`pagamento_id`) REFERENCES `pagamentos` (`pagamento_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
