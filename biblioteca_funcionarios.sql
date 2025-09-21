-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/09/2025 às 17:55
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
-- Banco de dados: `biblioteca_funcionarios`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `idCliente` int(11) NOT NULL,
  `Login` varchar(20) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Senha` varchar(30) NOT NULL,
  `E-mail` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`idCliente`, `Login`, `Nome`, `Senha`, `E-mail`) VALUES
(1, 'Felps', 'Felipe Rovesta', 'RONIL333', 'rovestaf@gmail.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `emprestimos`
--

CREATE TABLE `emprestimos` (
  `idEmprestimo` int(11) NOT NULL,
  `idFunc` int(11) NOT NULL,
  `idLivro` int(11) NOT NULL,
  `data_emprestimo` date NOT NULL,
  `data_prevista` date NOT NULL,
  `data_devolucao` date DEFAULT NULL,
  `status` enum('ativo','devolvido','atrasado') DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `emprestimos`
--

INSERT INTO `emprestimos` (`idEmprestimo`, `idFunc`, `idLivro`, `data_emprestimo`, `data_prevista`, `data_devolucao`, `status`) VALUES
(1, 1, 2, '2025-08-22', '2025-08-29', '2025-08-22', 'devolvido'),
(2, 17, 1, '2025-08-22', '2025-08-29', '2025-08-22', 'devolvido'),
(3, 2, 2, '2025-08-22', '2025-08-23', '2025-08-22', 'devolvido');

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `idFunc` int(11) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `nome_completo` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `funcao` enum('gerente','repositor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `funcionarios`
--

INSERT INTO `funcionarios` (`idFunc`, `nickname`, `senha_hash`, `nome_completo`, `email`, `funcao`) VALUES
(1, 'Gustavo', '$2y$10$4Adifhs4bgjg0Qt8e.GeDeR6Bnp0waNUY8eOZh6kvGMzXdKGzPwje', 'Gustavo', 'tigorfoda@gmail.com', 'gerente'),
(2, 'aa', '$2y$10$NORTgBFQYngTMJeGoWS6.OayJkI5NvNN1wZ9zNZxg8YGRAiyyCdLm', 'gustaokk', 'tigorfoda@gmail.com', 'repositor'),
(3, 'Emily', '$2y$10$RBRP7aaAb0GCOllJaZkas.LrpuNz0Mi/qnfWVvtln20jeWYk7eNb.', 'Emily 123', 'emilylinda@hotmail.com', 'repositor'),
(4, 'Arthur', '$2y$10$ZnuMPiTvQ9nKd44pcRDl8eBGFVs5PvrETyI8IsV8jGLIh0WCm2pte', 'Arthur Neris', 'arthur@gmail.com', 'repositor'),
(5, 'Felipe', '$2y$10$pv/aIMS/5EneJ0FsfCOptOYEeJftHWbP3F4ZO7WBMqvForrihcDCi', 'Felipe Rovesta', 'felipe@gmail.com', 'repositor'),
(6, 'Ronildo', '$2y$10$1krR0Pvj1VbN32iu6qriMOXdRmX9I4I64MVQjXrnDuwQO.YcpECWe', 'Ronildo Aparecido Ferreira', 'ronildao.vav@yahoo.com', 'gerente'),
(17, 'Amadeu', '$2y$10$83lbVcEwrEUYABPPDi9j..sNbboH8BQYEyH/Vzxr8am/E5c6FW/02', 'Amadeu da Cruz Bresciani', 'amadeu.amadeu@gmail.com', 'gerente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `livros`
--

CREATE TABLE `livros` (
  `idLivro` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `Sessão` varchar(30) NOT NULL,
  `autor` varchar(150) NOT NULL,
  `editora` varchar(100) DEFAULT NULL,
  `ano_publicacao` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `livros`
--

INSERT INTO `livros` (`idLivro`, `titulo`, `Sessão`, `autor`, `editora`, `ano_publicacao`, `quantidade`, `data_cadastro`) VALUES
(1, '1984', 'Romance', 'George Orwell', 'Secker and Warburg', 1949, 5, '2025-06-04 17:34:37'),
(2, 'O Poder do Hábito', 'Autoajuda', 'Charles Duhigg', 'Random House', 2012, 20, '2025-06-10 16:24:55');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idCliente`),
  ADD UNIQUE KEY `Unico` (`Login`);

--
-- Índices de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD PRIMARY KEY (`idEmprestimo`),
  ADD KEY `fk_funcionario` (`idFunc`),
  ADD KEY `fk_livro` (`idLivro`);

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`idFunc`);

--
-- Índices de tabela `livros`
--
ALTER TABLE `livros`
  ADD PRIMARY KEY (`idLivro`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idCliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  MODIFY `idEmprestimo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `idFunc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `livros`
--
ALTER TABLE `livros`
  MODIFY `idLivro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
