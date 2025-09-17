<?php
    session_start(); 
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>

    <meta charset="UTF-8">
    <title>Início</title>
     <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    <script src="js/bootstrap.bundle.min.js"></script>

</head>
<body class="d-flex align-items-center justify-content-center vh-50">
    <div class="container">
    <div class="row justify-content-center">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <h3>Bem-vindo, <?= htmlspecialchars($_SESSION['nickname']) ?>!</h3>

        <form method="POST">
            <button type="submit" name="mudar" value="true">Mudar senha</button>
        </form>

        <?php 
        if (isset($_POST['mudar']) && $_POST['mudar'] == 'true'): ?>
            <form method="POST" action="mudar_senha.php">
                <div>
                    <label>Senha antiga:</label>
                    <input type="password" name="senha_antiga" required>
                </div>
                <div>
                    <label>Nova senha:</label>
                    <input type="password" name="nova_senha" required>
                </div>
                <button type="submit">Confirmar</button>
            </form>
        <?php endif; ?>

        <br>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <nav class="navbar navbar-expand-lg rounded mt-4">
                    <div class="container-fluid justify-content-center">
                        <ul class="navbar-nav d-flex flex-row gap-3">
                            <li class="nav-item">
                                <a class="nav-link " href="entrada_produto.php">Entrada do Produto</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="lista.php">Lista dos Livros</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " href="emprestimos.php">Empréstimos</a>
                            </li>
                            <br>
                            
                         
                        </ul>
                    </div>
                </nav>
                <?php if ($_SESSION['funcao'] === 'gerente'): ?>
                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <nav class="navbar navbar-expand-lg rounded mt-4">
                    <div class="container-fluid justify-content-center">
                        <ul class="navbar-nav d-flex flex-row gap-3">
                            <li class="nav-item">
                            <a href="cadastro_livro.php">Cadastrar livro</a> 
                            </li>
                            <li class="nav-item">
                            <a href="paniel.php">Painel de controle</a>
                            </li>
                            <li class="nav-item">
                            <a href="cadastro.php">Cadastrar usuário</a>
                            </li>
                            <br>
                            
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="logout.php">Sair</a>
                            </li>
                        </ul>
                    </div>
                </nav>
                </div>
                <?php endif; ?>

 </div>

 </div>
    </div>
    </div>
    </div>
</body>
</html>
