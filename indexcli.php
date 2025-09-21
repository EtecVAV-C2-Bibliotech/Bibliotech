<?php
session_start();
require 'banco.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['logado']) || empty($_SESSION['nickname'])) {
    header("Location: login.php");
    exit;
}

// Pega dados do usuário na tabela funcionarios
$nickname = $_SESSION['nickname'];
$sql = "SELECT * FROM funcionarios WHERE nickname = ?";
$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, "s", $nickname);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
mysqli_close($connect);

// Se for gerente, manda para o painel normal
if ($usuario['funcao'] === 'gerente') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Painel do Funcionário</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="hero inicio d-flex align-items-center justify-content-center vh-100">
    <div class="container text-center">
        <h1>Bem-vindo, <?= htmlspecialchars($usuario['nome_completo']) ?>!</h1>
        <p>Escolha uma das opções abaixo:</p>

        <div class="d-grid gap-3 mt-4">
            <a href="livros.php" class="btn btn-primary btn-lg">Ver livros disponíveis</a>
            <a href="infoc.php" class="btn btn-secondary btn-lg">Alterar informações</a>
            <a href="solicitacoes.php" class="btn btn-success btn-lg">Meus empréstimos</a>
            <a href="logout.php" class="btn btn-danger btn-lg">Sair</a>
        </div>
    </div>
</body>
</html>
