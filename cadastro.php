<?php
session_start(); 
if (!isset($_SESSION['logado']) || $_SESSION['funcao'] !== 'gerente') {
    header("Location: login.php");
    exit;
}
require 'banco.php';

function validarSenha($senha) {
    $temMaiuscula   = preg_match('/[A-Z]/', $senha);
    $temMinuscula   = preg_match('/[a-z]/', $senha);
    $temNumero      = preg_match('/[0-9]/', $senha);
    $temEspecial    = preg_match('/[^a-zA-Z0-9]/', $senha); // true se tiver especial
    $tamanhoValido  = strlen($senha) >= 6 && strlen($senha) <= 16;

    // regra: precisa maiúscula, minúscula e número, mas NÃO pode especial
    return $temMaiuscula && $temMinuscula && $temNumero && !$temEspecial && $tamanhoValido;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname       = trim($_POST['nickname'] ?? '');
    $senha          = $_POST['senha'] ?? '';
    $cpf            = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
    $nome_completo  = trim($_POST['nome_completo'] ?? '');
    $email          = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $funcao         = strtolower($_POST['funcao'] ?? ''); // deixa tudo em minúsculo

    if (!$nickname || !$senha || !$nome_completo || !$email || !$funcao || !$cpf) {
        $erro = "Por favor, preencha todos os campos.";
    } else if (!validarSenha($senha)) {
        $erro = "A senha deve conter pelo menos uma letra maiúscula, uma minúscula, um número, sem caracteres especiais e ter entre 6 e 16 caracteres.";
    } else {
        // verificar duplicados
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

            if (!mysqli_stmt_execute($stmt_insert)) {
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
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="hero cadastro_pessoa">
    <div class="container mt-5">
        <h2 class="mb-4">Cadastro</h2>

        <?php if ($erro): ?>
            <p class="erro text-danger"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="POST" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Usuário:</label>
                <input type="text" name="nickname" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Senha:</label>
                <input type="password" name="senha" class="form-control" required>
            </div>
            <div class="col-md-12">
                <label class="form-label">Nome completo:</label>
                <input type="text" name="nome_completo" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">CPF:</label>
                <input type="text" name="cpf" maxlength="14" placeholder="000.000.000-00" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Função:</label>
                <select name="funcao" class="form-select" required>
                    <option value="gerente">Gerente</option>
                    <option value="repositor">Repositor</option>
                    <option value="cliente">Cliente</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success">Cadastrar</button>
                <a href="index.php" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
    </div>
</body>
</html>
