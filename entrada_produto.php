<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

require 'banco.php';

$erro = '';
$sucesso = '';

$livros = [];
$sqlLivros = "SELECT idLivro, titulo FROM livros ORDER BY titulo";
$resultado = mysqli_query($connect, $sqlLivros);
if ($resultado) {
    while ($row = mysqli_fetch_assoc($resultado)) {
        $livros[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idLivro = intval($_POST['idLivro'] ?? 0);
    $quantidadeNova = intval($_POST['quantidade'] ?? 0);

    if ($idLivro <= 0 || $quantidadeNova <= 0) {
        $erro = "Selecione um livro e insira uma quantidade válida.";
    } else {
        $sqlUpdate = "UPDATE livros SET quantidade = quantidade + ? WHERE idLivro = ?";
        $stmt = mysqli_prepare($connect, $sqlUpdate);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $quantidadeNova, $idLivro);
            if (mysqli_stmt_execute($stmt)) {
                $sucesso = "Entrada registrada com sucesso!";
            } else {
                $erro = "Erro ao atualizar quantidade: " . mysqli_error($connect);
            }
            mysqli_stmt_close($stmt);
        } else {
            $erro = "Erro na preparação da consulta.";
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
    <title>Entrada de Produtos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="hero entra_produ">
<div class="container ">
    <h2>Registrar Entrada de Produtos</h2>
    <?php if ($erro): ?>
        <p class="erro"><?= htmlspecialchars($erro) ?></p>
    <?php elseif ($sucesso): ?>
        <p class="sucesso"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label>Livro:</label>
            <select name="idLivro" required>
                <option value="">-- Selecione um livro --</option>
                <?php foreach ($livros as $livro): ?>
                    <option value="<?= $livro['idLivro'] ?>">
                        <?= htmlspecialchars($livro['titulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Quantidade recebida:</label>
            <input type="number" name="quantidade" min="1" required>
        </div>
        <button type="submit">Registrar Entrada</button>
    </form>

    <br><a href="index.php">Voltar</a>
</div>
</body>
</html>
