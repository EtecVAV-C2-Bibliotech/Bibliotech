-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/09/2025 às 21:07
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
(23, 'Felps', '$2y$10$2XNXkzCdc7UBtRN09K7pdeSVTO70q4htRfu8aWs4Ybl1/AA/5I4De', 'Felipe é uma besta burra', 'coisinhaburra@outlook.com.br', '43468875890', 'cliente');

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
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `livros`
--

INSERT INTO `livros` (`idLivro`, `titulo`, `sessao`, `autor`, `editora`, `ano_publicacao`, `quantidade`, `data_cadastro`) VALUES
(1, '1984', 'Romance', 'George Orwell', 'Secker and Warburg', 1949, 5, '2025-06-04 17:34:37'),
(2, 'O Poder do Hábito', 'Autoajuda', 'Charles Duhigg', 'Random House', 2012, 5, '2025-06-10 16:24:55'),
(3, 'A Hipotese do Amor', 'Romance', 'Ali Hazelwood', 'Berkley Books', 2021, 3, '2025-09-21 17:41:19');

--
-- Índices para tabelas despejadas
--

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
-- AUTO_INCREMENT de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  MODIFY `idEmprestimo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `idFunc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `livros`
--
ALTER TABLE `livros`
  MODIFY `idLivro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD CONSTRAINT `fk_funcionario` FOREIGN KEY (`idFunc`) REFERENCES `funcionarios` (`idFunc`),
  ADD CONSTRAINT `fk_livro` FOREIGN KEY (`idLivro`) REFERENCES `livros` (`idLivro`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
