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
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="container">
        <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['nickname']) ?>!</h1>

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

        <?php if ($_SESSION['funcao'] === 'gerente'): ?>
           <a href="cadastro_livro.php">Cadastrar livro</a> | <a href="paniel.php">Painel de controle</a> | <a href="cadastro.php">Cadastrar usuário</a><br>
        <?php endif; ?>
        <a href="entrada_produto.php">Entrada do Produto | </a>
        <a href="lista.php">Lista dos livros | </a>
        <a href="emprestimos.php">Empréstimos | </a>
        <a href="logout.php">Sair</a>
    </div>
</body>
</html>
