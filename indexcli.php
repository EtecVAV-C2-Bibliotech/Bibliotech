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
    <title>Painel do cliente</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="hero inicio d-flex align-items-center justify-content-center vh-100">
    <div class="container text-center">
        <h1>Bem-vindo, <?= htmlspecialchars($usuario['nickname']) ?>!</h1>
        <p>Escolha uma das opções abaixo:</p>
        <form method="POST">
        <button type="submit" name="mudar" value="true" class="btn btn-warning mt-2">Mudar senha</button>
    </form>

    <?php if (isset($_POST['mudar']) && $_POST['mudar'] == 'true'): ?>
        <form method="POST" action="mudar_senha.php" class="mt-3">
            <div class="mb-2">
                <label class="form-label">Senha antiga:</label>
                <input type="password" name="senha_antiga" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Nova senha:</label>
                <input type="password" name="nova_senha" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Confirmar</button>
        </form>
    <?php endif; ?>
        <nav class="navbar navbar-expand-lg rounded mt-4">
            <div class="container-fluid justify-content-center">
                <ul class="navbar-nav d-flex flex-row gap-3">
                    <li class="nav-item">
                        <a href="livros.php" class="nav-link">Ver livros disponíveis</a>
                    </li>
                    <li class="nav-item">
                        <a href="comprar.php" class="nav-link">Comprar livros</a>
                    </li>
                    <li class="nav-item">
                        <a href="infoc.php" class="nav-link">Alterar informações</a>
                    </li>
                    <li class="nav-item">
                        <a href="solicitacoes.php" class="nav-link">Meus empréstimos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </nav>
        </div>
    </div>
</body>
</html>
