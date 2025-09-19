<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['logado']) || $_SESSION['funcao'] !== 'gerente') {
    header("Location: login.php");
    exit;
}

require 'banco.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $editora = trim($_POST['editora'] ?? '');
    $ano = intval($_POST['ano'] ?? 0);
    $quantidade = intval($_POST['quantidade'] ?? 0);

    if (!$titulo || !$autor || $quantidade <= 0) {
        $erro = "Preencha pelo menos Título, Autor e Quantidade.";
    } else {
        $sql = "INSERT INTO livros (titulo, autor, editora, ano_publicacao, quantidade) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connect, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssii", $titulo, $autor, $editora, $ano, $quantidade);
            if (mysqli_stmt_execute($stmt)) {
                $sucesso = "Livro cadastrado com sucesso!";
            } else {
                $erro = "Erro ao inserir no banco: " . mysqli_error($connect);
            }
            mysqli_stmt_close($stmt);
        } else {
            $erro = "Erro ao preparar query.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <title>Cadastro de Livro</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="hero cadastro">
<div class="container">
    <h2>Cadastro de Livro</h2>
    <?php if ($erro): ?>
        <p class="erro"><?= htmlspecialchars($erro) ?></p>
    <?php elseif ($sucesso): ?>
        <p class="sucesso"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>
    <form method="POST">
        <div><label>Título:</label><input type="text" name="titulo" required></div>
        <div><label>Autor:</label><input type="text" name="autor" required></div>
        <div><label>Editora:</label><input type="text" name="editora"></div>
        <div><label>Ano de Publicação:</label><input type="number" name="ano" min="1000" max="2100"></div>
        <div><label>Quantidade:</label><input type="number" name="quantidade" min="1" required></div>
        <button type="submit">Cadastrar Livro</button>
    </form>
    <a href="index.php">Voltar</a>
</div>
</body>
</html>
