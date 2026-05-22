-- ============================================================
-- QuadToys – Criação da tabela administradores
-- Execute no phpMyAdmin: banco colecionaveis > Importar
-- ============================================================

CREATE TABLE IF NOT EXISTS `administradores` (
  `admin_id`      INT(11)      NOT NULL AUTO_INCREMENT,
  `nome`          VARCHAR(100) NOT NULL,
  `email`         VARCHAR(150) NOT NULL,
  `senha`         VARCHAR(255) NOT NULL,
  `nivel_acesso`  ENUM('super','gerente') NOT NULL DEFAULT 'gerente',
  `data_cadastro` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acesso` DATETIME         NULL DEFAULT NULL,
  `status`        ENUM('ativo','inativo') NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
