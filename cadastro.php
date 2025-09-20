<?php
    session_start(); 
    if (!isset($_SESSION['logado']) || $_SESSION['funcao'] !== 'gerente') {
        header("Location: login.php");
        exit;
    }
require 'banco.php';

function validarSenha($senha) {
    $temMaiuscula = preg_match('/[A-Z]/', $senha);
    $temMinuscula = preg_match('/[a-z]/', $senha);
    $temEspecial = preg_match('/[^a-zA-Z0-9]/', $senha);
    $tamanhoValido = strlen($senha) >= 6 && strlen($senha) <= 16;
    return $temMaiuscula && $temMinuscula && !$temEspecial && $tamanhoValido;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
    $nome_completo = trim($_POST['nome_completo'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $funcao = $_POST['funcao'] ?? '';

    if (!$nickname || !$senha || !$nome_completo || !$email || !$funcao || !$cpf) {
        $erro = "Por favor, preencha todos os campos.";
    } else if (strlen($senha) < 6 || strlen($senha) > 16) {
        $erro = "A senha deve ter entre 6 e 16 caracteres.";
    } else if (!validarSenha($senha)) {
        $erro = "A senha deve conter pelo menos uma letra maiúscula, uma minúscula, sem caracteres especiais e ter entre 6 e 16 caracteres.";
    } else {

        $sql_check = "SELECT idFunc FROM funcionarios WHERE cpf = ? OR nickname = ?";
$stmt_check = mysqli_prepare($connect, $sql_check);
mysqli_stmt_bind_param($stmt_check, "ss", $cpf, $nickname);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_store_result($stmt_check);

if (mysqli_stmt_num_rows($stmt_check) > 0) {
    $erro = "CPF ou usuário já cadastrado.";
} else {
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $sql_insert = "INSERT INTO funcionarios (nickname, senha_hash, nome_completo, email, funcao, cpf) 
                   VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($connect, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "ssssss", $nickname, $senha_hash, $nome_completo, $email, $funcao, $cpf);

    if (mysqli_stmt_execute($stmt_insert)) {
        
    } else {
        $erro = "Erro ao inserir: " . mysqli_error($connect);
    }
    mysqli_stmt_close($stmt_insert);
}
mysqli_stmt_close($stmt_check);
       
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/bootstrap.bundle.min.js"></script>
    <title>Cadastro</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="hero cadastro_pessoa">
    <div class="container">
        <h2>Cadastro</h2>
        <?php if ($erro): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>
        <form method="POST">
            <div>
                <label>Usuário:</label>
                <input type="text" name="nickname" required>
            </div>
            <div>
                <label>Senha:</label>
                <input type="password" name="senha" required>
            </div>
            <div>
                <label>Nome completo:</label>
                <input type="text" name="nome_completo" required>
            </div>
             <div>
                <label>CPF:</label>
               <input type="text" name="cpf" maxlength="14" placeholder="000.000.000-00" required>
            <div>
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div>
                <label>Função:</label>
                <select name="funcao" required>
                    <option value="gerente">Gerente</option>
                    <option value="repositor">Repositor</option>
                </select>
            </div>
            <button type="submit">Cadastrar</button>
        </form>
        <a href="index.php">Voltar</a>
    </div>
</body>
</html>
