<?php
// processar_compra.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'banco.php'; // deve definir $connect

if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

$idCliente = $_SESSION['idFunc'] ?? null;
if (!$idCliente) {
    die("Erro: id do usuário não encontrado na sessão.");
}

$idLivro = intval($_POST['idLivro'] ?? 0);
$qtd = intval($_POST['qtd'] ?? 0);

if ($idLivro <= 0 || $qtd <= 0) {
    header("Location: comprar.php?msg=" . urlencode("Quantidade ou livro inválido."));
    exit;
}

/* 1) Garante existência da tabela compras */
$sqlCreate = "CREATE TABLE IF NOT EXISTS compras (
    idCompra INT AUTO_INCREMENT PRIMARY KEY,
    idCliente INT NOT NULL,
    idLivro INT NOT NULL,
    quantidade INT NOT NULL,
    valor_unitario DECIMAL(10,2) NOT NULL,
    data_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idCliente) REFERENCES funcionarios(idFunc),
    FOREIGN KEY (idLivro) REFERENCES livros(idLivro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if (!mysqli_query($connect, $sqlCreate)) {
    die("Erro ao criar tabela compras: " . mysqli_error($connect));
}

/* 2) Busca livro e estoque */
$stmt = mysqli_prepare($connect, "SELECT titulo, quantidade FROM livros WHERE idLivro = ?");
mysqli_stmt_bind_param($stmt, "i", $idLivro);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$livro = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$livro) {
    header("Location: comprar.php?msg=" . urlencode("Livro não encontrado."));
    exit;
}

if ($livro['quantidade'] < $qtd) {
    header("Location: comprar.php?msg=" . urlencode("Estoque insuficiente."));
    exit;
}

/* 3) Define preço (se ainda não tem coluna preço, usamos valor fixo) */
/* Se você adicionar coluna preco em livros, troque por SELECT preco acima. */
$valor_unitario = 25.00;

/* 4) Inicia transação para segurança */
mysqli_begin_transaction($connect);

$ok = true;

/* 4.1) Inserir compra */
$stmtIns = mysqli_prepare($connect, "INSERT INTO compras (idCliente, idLivro, quantidade, valor_unitario) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmtIns, "iiid", $idCliente, $idLivro, $qtd, $valor_unitario);
if (!mysqli_stmt_execute($stmtIns)) {
    $ok = false;
    $erro = "Erro ao registrar compra: " . mysqli_error($connect);
}
mysqli_stmt_close($stmtIns);

/* 4.2) Atualizar estoque */
if ($ok) {
    $stmtUpd = mysqli_prepare($connect, "UPDATE livros SET quantidade = quantidade - ? WHERE idLivro = ? AND quantidade >= ?");
    mysqli_stmt_bind_param($stmtUpd, "iii", $qtd, $idLivro, $qtd);
    if (!mysqli_stmt_execute($stmtUpd) || mysqli_stmt_affected_rows($stmtUpd) === 0) {
        $ok = false;
        $erro = "Erro ao atualizar estoque (pode ter sido alterado por outro processo).";
    }
    mysqli_stmt_close($stmtUpd);
}

if ($ok) {
    mysqli_commit($connect);
    header("Location: comprar.php?msg=" . urlencode("Compra efetuada com sucesso!"));
    exit;
} else {
    mysqli_rollback($connect);
    header("Location: comprar.php?msg=" . urlencode($erro ?? "Erro desconhecido."));
    exit;
}
