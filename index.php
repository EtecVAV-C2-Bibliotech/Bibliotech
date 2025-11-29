<?php
session_start(); 

// Se não estiver logado, manda para login
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

// Se for cliente, manda direto para indexcli
if (isset($_SESSION['funcao']) && $_SESSION['funcao'] === 'cliente') {
    header("Location: indexcli.php");
    exit;
}

// A partir daqui, é funcionário ou gerente
$nickname = $_SESSION['nickname'] ?? '';
$funcao = $_SESSION['funcao'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body class="hero painel">
<div class="container mt-5">
    <h3>Bem-vindo, <?= htmlspecialchars($nickname) ?>!</h3>

    <!-- Form mudar senha -->
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

    <!-- Navbar comum para todos funcionários -->
    <nav class="navbar navbar-expand-lg rounded mt-4">
        <div class="container-fluid justify-content-center">
            <ul class="navbar-nav d-flex flex-row gap-3">
                <li class="nav-item">
                    <a class="nav-link" href="entrada_produto.php">Entrada do Produto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="lista.php">Lista dos Livros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="emprestimos.php">Empréstimos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="comprar.php">Comprar Livros</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Opções extras apenas para gerente -->
    <?php if ($funcao === 'gerente'): ?>
        <nav class="navbar navbar-expand-lg rounded mt-4">
            <div class="container-fluid justify-content-center">
                <ul class="navbar-nav d-flex flex-row gap-3">
                    <li class="nav-item">
                        <a class="nav-link" href="cadastro_livro.php">Cadastrar livro</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="paniel.php">Painel de controle</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cadastro.php">Cadastrar usuário</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </nav>
    <?php else: ?>
        <div class="mt-3">
            <a href="logout.php" class="btn btn-outline-danger">Sair</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
