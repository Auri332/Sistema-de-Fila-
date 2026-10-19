-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 24/05/2025 às 16:23
-- Versão do servidor: 9.1.0
-- Versão do PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `atendimento_novo`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--
-- Criação: 23/05/2025 às 10:59
-- Última atualização: 23/05/2025 às 10:59
--

DROP TABLE IF EXISTS `configuracoes`;
CREATE TABLE IF NOT EXISTS `configuracoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fonte` varchar(50) DEFAULT NULL,
  `tamanho` int DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `senhas`
--
-- Criação: 24/05/2025 às 15:14
-- Última atualização: 24/05/2025 às 16:21
--

DROP TABLE IF EXISTS `senhas`;
CREATE TABLE IF NOT EXISTS `senhas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) DEFAULT 'geral',
  `codigo` varchar(10) DEFAULT NULL,
  `servico` varchar(50) DEFAULT 'geral',
  `status` varchar(20) DEFAULT 'aguardando',
  `atendente` varchar(100) DEFAULT NULL,
  `balcao` varchar(50) DEFAULT NULL,
  `chamado_em` datetime DEFAULT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  `visivel_painel` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `senhas`
--

INSERT INTO `senhas` (`id`, `tipo`, `codigo`, `servico`, `status`, `atendente`, `balcao`, `chamado_em`, `data_criacao`, `visivel_painel`) VALUES
(117, 'geral', 'G001', 'geral', 'chamado', 'Marta Anastacia', '4', '2025-05-24 16:52:52', '2025-05-24 16:52:17', 1),
(118, 'isenção', 'I001', 'isenção', 'chamado', 'Mirian Matias', '4', '2025-05-24 16:57:53', '2025-05-24 16:57:17', 1),
(119, 'prioridade', 'P001', 'prioridade', 'aguardando', NULL, NULL, NULL, '2025-05-24 16:57:23', 0),
(120, 'geral', 'G002', 'geral', 'chamado', 'Marta Anastacia', '4', '2025-05-24 16:58:59', '2025-05-24 16:57:25', 1),
(121, 'geral', 'G003', 'geral', 'chamado', 'Marta Anastacia', '4', '2025-05-24 17:01:48', '2025-05-24 17:01:37', 0),
(122, 'geral', 'G004', 'geral', 'chamado', 'Marta Anastacia', '4', '2025-05-24 17:09:47', '2025-05-24 17:09:34', 1),
(123, 'geral', 'G005', 'geral', 'chamado', 'Marta Anastacia', '4', '2025-05-24 17:10:14', '2025-05-24 17:09:36', 1),
(124, 'isenção', 'I002', 'isenção', 'chamado', 'Mirian Matias', '4', '2025-05-24 17:12:16', '2025-05-24 17:12:01', 1),
(125, 'geral', 'G006', 'geral', 'nao_apareceu', 'Marta Anastacia', '4', '2025-05-24 17:15:43', '2025-05-24 17:15:05', 0),
(126, 'geral', 'G007', 'geral', 'chamado', 'Marta Anastacia', '4', '2025-05-24 17:21:08', '2025-05-24 17:20:21', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--
-- Criação: 23/05/2025 às 11:26
-- Última atualização: 23/05/2025 às 11:34
--

DROP TABLE IF EXISTS `servicos`;
CREATE TABLE IF NOT EXISTS `servicos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `servicos`
--

INSERT INTO `servicos` (`id`, `nome`) VALUES
(1, 'Atendimento Geral'),
(2, 'Financeiro'),
(3, 'Suporte Técnico'),
(4, 'RH'),
(5, 'Atendimento Geral'),
(6, 'Financeiro'),
(7, 'Suporte Técnico');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--
-- Criação: 24/05/2025 às 09:47
-- Última atualização: 24/05/2025 às 09:49
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','atendente','cliente','outro') NOT NULL,
  `balcao` varchar(50) DEFAULT NULL,
  `servico` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `usuario`, `senha`, `perfil`, `balcao`, `servico`) VALUES
(1, 'Aurielson Kahuva', 'aurielson.kahuva', '1234', 'admin', '1', ''),
(23, 'Danielaa', 'dani', '1234', 'atendente', '5', 'prioridade'),
(16, 'Mirian Matias', 'mirian', 'senha123', 'atendente', '4', 'isenção'),
(22, 'Daniela Patrocinio', 'daniela', '1234', 'atendente', '1', 'geral'),
(21, 'Mirian Matias', 'marta1', 'senha123', 'atendente', '2', 'geral'),
(13, 'Hospital Central do Lubango', 'hcl', '1234', 'cliente', '1', ''),
(15, 'Aurielson Kahuva', 'aurielson', 'senha123', 'admin', NULL, NULL),
(20, 'Marta Anastacia', 'marta', '1234', 'atendente', '4', 'geral');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
