<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
require 'banco.php';

if (!isset($_SESSION['logado']) || !isset($_SESSION['nickname'])) {
    header("Location: login.php");
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nickname = $_SESSION['nickname']; 
    $senha_antiga = $_POST['senha_antiga'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';

    function validarSenha($senha) {
        $temMaiuscula = preg_match('/[A-Z]/', $senha);
        $temMinuscula = preg_match('/[a-z]/', $senha);
        $temEspecial = preg_match('/[^a-zA-Z0-9]/', $senha);
        $tamanhoValido = strlen($senha) >= 6 && strlen($senha) <= 16;
        return $temMaiuscula && $temMinuscula && !$temEspecial && $tamanhoValido;
    }

    if (!$senha_antiga || !$nova_senha) {
        $erro = "Preencha todos os campos.";
    } else if (strlen($nova_senha) < 6 || strlen($nova_senha) > 16) {
        $erro = "A nova senha deve ter entre 6 e 16 caracteres.";
    } else if (!validarSenha($nova_senha)) {
        $erro = "A nova senha deve conter pelo menos uma letra maiúscula, uma minúscula, sem caracteres especiais e ter entre 6 e 16 caracteres.";
    } else {
        $sql = "SELECT senha_hash FROM funcionarios WHERE nickname = ?";
        $stmt = mysqli_prepare($connect, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $nickname);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $usuario = mysqli_fetch_assoc($resultado);

            if ($usuario && password_verify($senha_antiga, $usuario['senha_hash'])) {
                $nova_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

                $sqlUpdate = "UPDATE funcionarios SET senha_hash = ? WHERE nickname = ?";
                $stmtUpdate = mysqli_prepare($connect, $sqlUpdate);
                if ($stmtUpdate) {
                    mysqli_stmt_bind_param($stmtUpdate, "ss", $nova_hash, $nickname);
                    if (mysqli_stmt_execute($stmtUpdate)) {
                        mysqli_stmt_close($stmtUpdate);
                        mysqli_stmt_close($stmt);
                        mysqli_close($connect);
                        header("Location: index.php");
                        exit;
                    } else {
                        $erro = "Erro ao atualizar senha.";
                    }
                } else {
                    $erro = "Erro na preparação da query de atualização.";
                }
            } else {
                $erro = "Senha antiga incorreta.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $erro = "Erro na consulta ao banco.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/bootstrap.bundle.min.js"></script>
    <title>Mudar Senha</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="container">
        <h2>Mudar Senha</h2>
        <?php if ($erro): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>
        <form method="POST">
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
        <a href="index.php">Voltar</a>
    </div>
</body>
</html>
