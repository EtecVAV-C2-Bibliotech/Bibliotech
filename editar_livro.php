<?php
if(!isset($_SESSION)) { 
    session_start(); 
} 
if (!isset($_SESSION['logado']) || $_SESSION['funcao'] !== 'gerente') {
    header("Location: login.php");
    exit;
}

require 'banco.php';

$id = intval($_GET['id'] ?? 0);
$erro = '';
$sucesso = '';

if ($id > 0) {
    $sql = "SELECT * FROM livros WHERE idLivro = ?";
    $stmt = mysqli_prepare($connect, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $livro = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($stmt);
    } else {
        $erro = "Erro ao preparar consulta.";
    }
} else {
    $erro = "ID inválido.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $editora = trim($_POST['editora'] ?? '');
    $ano = intval($_POST['ano'] ?? 0);
    $quantidade = intval($_POST['quantidade'] ?? 0);

    if (!$titulo || !$autor || $quantidade <= 0) {
        $erro = "Preencha pelo menos Título, Autor e Quantidade.";
    } else {
        $sql = "UPDATE livros SET titulo = ?, autor = ?, editora = ?, ano_publicacao = ?, quantidade = ? WHERE idLivro = ?";
        $stmt = mysqli_prepare($connect, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssiii", $titulo, $autor, $editora, $ano, $quantidade, $id);
            if (mysqli_stmt_execute($stmt)) {
                $sucesso = "Livro atualizado com sucesso!";
                $livro = [
                    'titulo' => $titulo,
                    'autor' => $autor,
                    'editora' => $editora,
                    'ano_publicacao' => $ano,
                    'quantidade' => $quantidade
                ];
            } else {
                $erro = "Erro ao atualizar: " . mysqli_error($connect);
            }
            mysqli_stmt_close($stmt);
        } else {
            $erro = "Erro ao preparar a atualização.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Livro</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="hero cadastro">
<div class="container">
    <h2>Editar Livro</h2>

    <?php if ($erro): ?>
        <p class="erro"><?= htmlspecialchars($erro) ?></p>
    <?php elseif ($sucesso): ?>
        <p class="sucesso"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>

    <?php if ($livro): ?>
        <form method="POST">
            <div><label>Título:</label><input type="text" name="titulo" value="<?= htmlspecialchars($livro['titulo']) ?>" required></div>
            <div><label>Autor:</label><input type="text" name="autor" value="<?= htmlspecialchars($livro['autor']) ?>" required></div>
            <div><label>Editora:</label><input type="text" name="editora" value="<?= htmlspecialchars($livro['editora']) ?>"></div>
            <div><label>Ano:</label><input type="number" name="ano" value="<?= htmlspecialchars($livro['ano_publicacao']) ?>"></div>
            <div><label>Quantidade:</label><input type="number" name="quantidade" min="0" value="<?= htmlspecialchars($livro['quantidade']) ?>" required></div>
            <button type="submit">Salvar Alterações</button>
        </form>
    <?php endif; ?>

    <br>
    <a href="lista.php">Voltar à Lista</a>
</div>
</body>
</html>
