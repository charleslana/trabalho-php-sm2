-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 18/11/2020 às 21:18
-- Versão do servidor: 10.4.14-MariaDB
-- Versão do PHP: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `blue`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_endereco`
--

CREATE TABLE `tb_endereco` (
  `endereco_id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) NOT NULL,
  `endereco` varchar(45) NOT NULL,
  `cidade` varchar(45) NOT NULL,
  `estado` varchar(45) NOT NULL,
  `numero` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `tb_endereco`
--

INSERT INTO `tb_endereco` (`endereco_id`, `usuario_id`, `endereco`, `cidade`, `estado`, `numero`) VALUES
(1, 1, 'Rua dos Humildes', 'Betim', 'Minas Gerais', 1000);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_telefone`
--

CREATE TABLE `tb_telefone` (
  `telefone_id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) NOT NULL,
  `telefone` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `tb_telefone`
--

INSERT INTO `tb_telefone` (`telefone_id`, `usuario_id`, `telefone`) VALUES
(1, 1, 31999999999);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_usuario`
--

CREATE TABLE `tb_usuario` (
  `id` bigint(20) NOT NULL,
  `email` varchar(45) NOT NULL,
  `senha` varchar(45) NOT NULL,
  `ocupacao` enum('Pescador','Mercado') NOT NULL,
  `nome` varchar(20) NOT NULL,
  `sobrenome` varchar(20) NOT NULL,
  `descricao` varchar(1000) NOT NULL,
  `banner` varchar(255) NOT NULL,
  `registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `token` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `tb_usuario`
--

INSERT INTO `tb_usuario` (`id`, `email`, `senha`, `ocupacao`, `nome`, `sobrenome`, `descricao`, `banner`, `registro`, `token`) VALUES
(1, 'teste@teste.com', 'aa1bf4646de67fd9086cf6c79007026c', 'Pescador', 'Charles', 'Lana', 'Testando a nova plataforma de mercado de frutos do mar.', 'cc95262bec809b0e9ab3656201ef5672df9974b38e0f0fccd1ae78302cdfc495.jpg', '2020-11-18 20:17:49', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `tb_endereco`
--
ALTER TABLE `tb_endereco`
  ADD PRIMARY KEY (`endereco_id`),
  ADD KEY `fk_tb_endereco_tb_usuario_id` (`usuario_id`);

--
-- Índices de tabela `tb_telefone`
--
ALTER TABLE `tb_telefone`
  ADD PRIMARY KEY (`telefone_id`),
  ADD KEY `fk_tb_telefone_tb_usuario_id` (`usuario_id`);

--
-- Índices de tabela `tb_usuario`
--
ALTER TABLE `tb_usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tb_endereco`
--
ALTER TABLE `tb_endereco`
  MODIFY `endereco_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tb_telefone`
--
ALTER TABLE `tb_telefone`
  MODIFY `telefone_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tb_usuario`
--
ALTER TABLE `tb_usuario`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `tb_endereco`
--
ALTER TABLE `tb_endereco`
  ADD CONSTRAINT `fk_tb_endereco_tb_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `tb_usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `tb_telefone`
--
ALTER TABLE `tb_telefone`
  ADD CONSTRAINT `fk_tb_telefone_tb_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `tb_usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
