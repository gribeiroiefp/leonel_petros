-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21-Ago-2025 às 17:32
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `website de livros`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `autores`
--

CREATE TABLE `autores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `nacionalidade` varchar(50) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `autores`
--

INSERT INTO `autores` (`id`, `nome`, `data_nascimento`, `nacionalidade`, `foto`) VALUES
(1, 'Fiódor Dostoiévski', '1821-11-11', 'Russa', NULL),
(2, 'Franz Kafka', '1883-07-03', 'Alemã', NULL),
(3, 'Sun Tzu', NULL, 'Chinesa', NULL),
(4, 'Maquiavel', '1469-05-03', 'Italiana', NULL),
(5, 'William Shakespeare', '1564-04-26', 'Britânica', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `autor_livro`
--

CREATE TABLE `autor_livro` (
  `autor_id` int(11) NOT NULL,
  `livro_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `autor_livro`
--

INSERT INTO `autor_livro` (`autor_id`, `livro_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(5, 6);

-- --------------------------------------------------------

--
-- Estrutura da tabela `livros`
--

CREATE TABLE `livros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `anos` year(4) DEFAULT NULL,
  `capa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `livros`
--

INSERT INTO `livros` (`id`, `titulo`, `anos`, `capa`) VALUES
(1, 'Noites Brancas', '0000', NULL),
(2, 'A Metamorfose', '1915', NULL),
(3, 'A Arte da Guerra', NULL, NULL),
(4, 'O Príncipe', '0000', NULL),
(5, 'Hamlet', '0000', NULL),
(6, 'Romeu e Julieta', '0000', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `autores`
--
ALTER TABLE `autores`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `autor_livro`
--
ALTER TABLE `autor_livro`
  ADD PRIMARY KEY (`autor_id`,`livro_id`),
  ADD KEY `livro_id` (`livro_id`);

--
-- Índices para tabela `livros`
--
ALTER TABLE `livros`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `autores`
--
ALTER TABLE `autores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `livros`
--
ALTER TABLE `livros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `autor_livro`
--
ALTER TABLE `autor_livro`
  ADD CONSTRAINT `autor_livro_ibfk_1` FOREIGN KEY (`autor_id`) REFERENCES `autores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `autor_livro_ibfk_2` FOREIGN KEY (`livro_id`) REFERENCES `livros` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
