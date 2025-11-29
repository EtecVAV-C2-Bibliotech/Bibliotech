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

    // Dados normais
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $sessao = trim($_POST['sessao'] ?? '');
    $editora = trim($_POST['editora'] ?? '');
    $ano = intval($_POST['ano'] ?? 0);
    $quantidade = intval($_POST['quantidade'] ?? 0);

    if (!$titulo || !$autor || $quantidade <= 0) {
        $erro = "Preencha pelo menos Título, Autor e Quantidade.";
    } else {

        // ============================
        // UPLOAD DA CAPA DO LIVRO
        // ============================

        $nomeCapa = null;

        if (isset($_FILES['capa']) && $_FILES['capa']['error'] === 0) {

            $pasta = "imagens/";

            // cria pasta caso não exista
            if (!is_dir($pasta)) {
                mkdir($pasta, 0777, true);
            }

            $ext = strtolower(pathinfo($_FILES['capa']['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg','jpeg','png','gif','webp'];

            if (!in_array($ext, $permitidas)) {
                $erro = "Formato inválido. Envie JPG, PNG, GIF ou WEBP.";
            } else {

                $nomeCapa = uniqid("capa_", true) . "." . $ext;
                $destino = $pasta . $nomeCapa;

                if (!move_uploaded_file($_FILES['capa']['tmp_name'], $destino)) {
                    $erro = "Erro ao fazer upload da imagem.";
                }
            }
        }

        if (!$erro) {
            $sql = "INSERT INTO livros (titulo, autor, sessao, editora, ano_publicacao, quantidade, capa) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($connect, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssiss",
                    $titulo, $autor, $sessao, $editora, $ano, $quantidade, $nomeCapa
                );

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

    <form method="POST" enctype="multipart/form-data">
        <div><label>Título:</label><input type="text" name="titulo" required></div>
        <div><label>Autor:</label><input type="text" name="autor" required></div>
        <div><label>Sessão:</label><input type="text" name="sessao" required></div>
        <div><label>Editora:</label><input type="text" name="editora"></div>
        <div><label>Ano de Publicação:</label><input type="number" name="ano" min="1000" max="2100"></div>
        <div><label>Quantidade:</label><input type="number" name="quantidade" min="1" required></div>

        <div><label>Capa do Livro:</label><input type="file" name="capa" accept="image/*"></div>

        <button type="submit">Cadastrar Livro</button>
    </form>

    <a href="index.php">Voltar</a>
</div>
</body>
</html>
