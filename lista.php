<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

require 'banco.php';

$sql = "SELECT idLivro, titulo, autor, editora, ano_publicacao, quantidade FROM livros ORDER BY titulo ASC";
$resultado = mysqli_query($connect, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <title>Lista de Livros</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<div class="container">
    <h2>Livros Cadastrados</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>Editora</th>
                <th>Ano</th>
                <th>Quantidade</th>
                <?php if ($_SESSION['funcao'] === 'gerente'): ?>
                    <th>Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($livro = mysqli_fetch_assoc($resultado)): ?>
                <tr>
                    <td><?= htmlspecialchars($livro['titulo']) ?></td>
                    <td><?= htmlspecialchars($livro['autor']) ?></td>
                    <td><?= htmlspecialchars($livro['editora']) ?></td>
                    <td><?= htmlspecialchars($livro['ano_publicacao']) ?></td>
                    <td><?= htmlspecialchars($livro['quantidade']) ?></td>
                    <?php if ($_SESSION['funcao'] === 'gerente'): ?>
                        <td><a href="editar_livro.php?id=<?= $livro['idLivro'] ?>">Editar</a></td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <br>
    <a href="index.php">Voltar</a>
</div>
</body>
</html>
