-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/11/2025 às 12:58
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
-- Estrutura para tabela `compras`
--

CREATE TABLE `compras` (
  `idCompra` int(11) NOT NULL,
  `idCliente` int(11) NOT NULL,
  `idLivro` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `data_compra` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `compras`
--

INSERT INTO `compras` (`idCompra`, `idCliente`, `idLivro`, `quantidade`, `valor_unitario`, `data_compra`) VALUES
(1, 4, 1, 1, 25.00, '2025-11-17 11:25:24'),
(2, 4, 1, 1, 25.00, '2025-11-17 11:25:27'),
(3, 4, 1, 1, 25.00, '2025-11-17 11:25:27'),
(4, 4, 1, 1, 25.00, '2025-11-17 11:25:27'),
(5, 4, 1, 1, 25.00, '2025-11-17 11:25:28'),
(6, 4, 1, 1, 25.00, '2025-11-17 11:25:28'),
(7, 4, 1, 1, 25.00, '2025-11-17 11:25:28'),
(8, 4, 1, 1, 25.00, '2025-11-17 11:25:28'),
(9, 4, 3, 1, 25.00, '2025-11-17 11:25:29'),
(10, 4, 3, 1, 25.00, '2025-11-17 11:25:30'),
(11, 4, 3, 1, 25.00, '2025-11-17 11:25:30'),
(12, 4, 2, 1, 25.00, '2025-11-17 11:25:31'),
(13, 4, 2, 1, 25.00, '2025-11-17 11:25:31'),
(14, 4, 2, 1, 25.00, '2025-11-17 11:25:32'),
(15, 4, 2, 1, 25.00, '2025-11-17 11:25:32'),
(16, 4, 2, 1, 25.00, '2025-11-17 11:25:32'),
(17, 4, 2, 1, 25.00, '2025-11-17 11:25:32'),
(18, 4, 2, 1, 25.00, '2025-11-17 11:25:32'),
(19, 4, 1, 11, 25.00, '2025-11-17 11:31:44'),
(20, 27, 3, 1, 25.00, '2025-11-28 11:46:02'),
(21, 27, 3, 1, 25.00, '2025-11-28 11:51:17'),
(22, 27, 3, 1, 25.00, '2025-11-28 11:51:19'),
(23, 27, 3, 1, 25.00, '2025-11-28 11:51:33'),
(24, 27, 3, 1, 25.00, '2025-11-28 11:52:28'),
(25, 27, 3, 1, 25.00, '2025-11-28 11:52:29'),
(26, 27, 2, 1, 25.00, '2025-11-28 11:52:30'),
(27, 27, 2, 1, 25.00, '2025-11-28 11:52:30'),
(28, 27, 2, 1, 25.00, '2025-11-28 11:52:51'),
(29, 27, 2, 1, 25.00, '2025-11-28 11:52:53'),
(30, 27, 2, 1, 25.00, '2025-11-28 11:52:53'),
(31, 1, 1, 1, 25.00, '2025-11-28 11:54:40'),
(32, 1, 1, 1, 25.00, '2025-11-28 11:58:20');

-- --------------------------------------------------------

--
-- Estrutura para tabela `emprestimos`
--

CREATE TABLE `emprestimos` (
  `idEmprestimo` int(11) NOT NULL,
  `idFunc` int(11) DEFAULT NULL,
  `idLivro` int(11) NOT NULL,
  `data_emprestimo` date NOT NULL,
  `data_prevista` date DEFAULT NULL,
  `data_devolucao` date DEFAULT NULL,
  `status` enum('ativo','devolvido','atrasado','solicitado') DEFAULT 'ativo',
  `idCliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `emprestimos`
--

INSERT INTO `emprestimos` (`idEmprestimo`, `idFunc`, `idLivro`, `data_emprestimo`, `data_prevista`, `data_devolucao`, `status`, `idCliente`) VALUES
(1, 1, 2, '2025-08-22', '2025-08-29', '2025-08-22', 'devolvido', 23),
(2, 17, 1, '2025-08-22', '2025-08-29', '2025-08-22', 'devolvido', 23),
(3, 2, 2, '2025-08-22', '2025-08-23', '2025-08-22', 'devolvido', 23),
(4, 1, 2, '2025-09-21', '2025-09-30', '2025-09-21', 'devolvido', 23),
(5, 1, 2, '2025-09-21', '2025-09-30', '2025-09-21', 'devolvido', 23),
(6, 6, 1, '2025-09-21', '2025-09-30', '2025-09-21', 'devolvido', 23),
(8, 4, 1, '2025-11-17', '2025-11-17', '2025-11-17', 'devolvido', 25),
(9, 4, 1, '2025-11-17', '2025-11-16', '2025-11-17', 'devolvido', 25);

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
  `cpf` varchar(14) NOT NULL,
  `funcao` enum('gerente','repositor','cliente') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `funcionarios`
--

INSERT INTO `funcionarios` (`idFunc`, `nickname`, `senha_hash`, `nome_completo`, `email`, `cpf`, `funcao`) VALUES
(1, 'Gustavo', '$2y$10$4Adifhs4bgjg0Qt8e.GeDeR6Bnp0waNUY8eOZh6kvGMzXdKGzPwje', 'Gustavo', 'tigorfoda@gmail.com', '111.111.111-11', 'gerente'),
(2, 'aa', '$2y$10$NORTgBFQYngTMJeGoWS6.OayJkI5NvNN1wZ9zNZxg8YGRAiyyCdLm', 'gustaokk', 'tigorfoda@gmail.com', '222.222.222-22', 'repositor'),
(3, 'Emily', '$2y$10$RBRP7aaAb0GCOllJaZkas.LrpuNz0Mi/qnfWVvtln20jeWYk7eNb.', 'Emily 123', 'emilylinda@hotmail.com', '333.333.333-33', 'repositor'),
(4, 'Arthur', '$2y$10$ZnuMPiTvQ9nKd44pcRDl8eBGFVs5PvrETyI8IsV8jGLIh0WCm2pte', 'Arthur Neris', 'arthur@gmail.com', '444.444.444-44', 'repositor'),
(5, 'Felipe', '$2y$10$pv/aIMS/5EneJ0FsfCOptOYEeJftHWbP3F4ZO7WBMqvForrihcDCi', 'Felipe Rovesta', 'felipe@gmail.com', '555.555.555-55', 'repositor'),
(6, 'Ronildo', '$2y$10$1krR0Pvj1VbN32iu6qriMOXdRmX9I4I64MVQjXrnDuwQO.YcpECWe', 'Ronildo Aparecido Ferreira', 'ronildao.vav@yahoo.com', '666.666.666-66', 'gerente'),
(17, 'Amadeu', '$2y$10$83lbVcEwrEUYABPPDi9j..sNbboH8BQYEyH/Vzxr8am/E5c6FW/02', 'Amadeu da Cruz Bresciani', 'amadeu.amadeu@gmail.com', '777.777.777-77', 'gerente'),
(18, 'Gustavo', '$2y$10$ArdDCcwnkPImjg3.ACGIxOcw6gaMwB6z8UtAzXjSryho4YjEpTq6O', 'Gustavo Henrique', 'gustavo@gmail.com', '80228202027', 'gerente'),
(20, 'Gustavo2', '$2y$10$V.zonwGizNmFsktKPifle.q.v2wwZbU9q8I6i0QGTNx4Q8hI16SJm', 'Gustavo Henrique', 'gustavo@gmail.com', '68119597036', 'gerente'),
(21, 'Gustavo2322', '$2y$10$2eIATwbAEiMbT2.aGopnIOkSnRMCHNHOsxqzReY.lanj451H3rqca', 'Gustavo Henrique', 'gustavo@gmail.com', '48911879053', 'gerente'),
(22, 'Gustavo2222', '$2y$10$TY5aXnmGHactCP6Q10wMduA0p0ZYboZlvV0Iy.1wmFq6EGjiwrDGy', 'Gustavo Henrique', 'tigorfoda@gmail.com', '58346938063', 'gerente'),
(23, 'Felps', '$2y$10$2XNXkzCdc7UBtRN09K7pdeSVTO70q4htRfu8aWs4Ybl1/AA/5I4De', 'Felipe é uma besta burra', 'coisinhaburra@outlook.com.br', '43468875890', 'cliente'),
(24, 'Felipe10', '$2y$10$tDd4FUj0XSpMBYQkkI9NwuX/3jOHXQOSyP247LcDxP6F9Z/HACUS.', 'Felipe é uma besta burra2', 'f@gmail.com', '41284566800', 'repositor'),
(25, 'Coisinha', '$2y$10$feI4uL17xDzluXYllqSSmOodcMA7yO9QVevQkgoJygDXvbPY093Ra', 'Coisa coisada', 'gfuig@gmail.com', '85356384289', 'cliente'),
(26, 'josefo', '$2y$10$Nmsydt4nmjxjkSndm6Zs0umQMHVZxVpkqEuzZrDYWXJIcxHWAaRz6', 'flavio josefo', 'josefo.josefo@gmail.com', '12345678901', 'cliente'),
(27, 'Eduardo', '$2y$10$GKFDO77.x1PpMvZVScUwIOFD01CFleTq74tJ9xIQchRe14no0NMeO', 'Eduardo da Cruz Gonçalves', 'eduardo.cruz@gmail.com', '50450450412', 'cliente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `livros`
--

CREATE TABLE `livros` (
  `idLivro` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `sessao` varchar(30) NOT NULL,
  `autor` varchar(150) NOT NULL,
  `editora` varchar(100) DEFAULT NULL,
  `ano_publicacao` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `livros`
--

INSERT INTO `livros` (`idLivro`, `titulo`, `sessao`, `autor`, `editora`, `ano_publicacao`, `quantidade`, `data_cadastro`, `imagem`) VALUES
(1, '1984', 'Romance', 'George Orwell', 'Secker and Warburg', 1949, 13, '2025-06-04 17:34:37', NULL),
(2, 'O Poder do Hábito', 'Autoajuda', 'Charles Duhigg', 'Random House', 2012, 15, '2025-06-10 16:24:55', NULL),
(3, 'A Hipotese do Amor', 'Romance', 'Ali Hazelwood', 'Berkley Books', 2021, 15, '2025-09-21 17:41:19', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`idCompra`),
  ADD KEY `idCliente` (`idCliente`),
  ADD KEY `idLivro` (`idLivro`);

--
-- Índices de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD PRIMARY KEY (`idEmprestimo`),
  ADD KEY `fk_funcionario` (`idFunc`),
  ADD KEY `fk_livro` (`idLivro`),
  ADD KEY `fk_idCliente` (`idCliente`);

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`idFunc`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `livros`
--
ALTER TABLE `livros`
  ADD PRIMARY KEY (`idLivro`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `compras`
--
ALTER TABLE `compras`
  MODIFY `idCompra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  MODIFY `idEmprestimo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `idFunc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `livros`
--
ALTER TABLE `livros`
  MODIFY `idLivro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`idCliente`) REFERENCES `funcionarios` (`idFunc`),
  ADD CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`idLivro`) REFERENCES `livros` (`idLivro`);

--
-- Restrições para tabelas `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD CONSTRAINT `fk_funcionario` FOREIGN KEY (`idFunc`) REFERENCES `funcionarios` (`idFunc`),
  ADD CONSTRAINT `fk_idCliente` FOREIGN KEY (`idCliente`) REFERENCES `funcionarios` (`idFunc`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_livro` FOREIGN KEY (`idLivro`) REFERENCES `livros` (`idLivro`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
