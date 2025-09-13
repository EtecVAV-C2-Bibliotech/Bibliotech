<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
require 'banco.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = $_POST['nickname'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!$nickname || !$senha) {
        $erro = "Preencha usuário e senha.";
    } else {
        $sql = "SELECT * FROM funcionarios WHERE nickname = ?";
        $stmt = mysqli_prepare($connect, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $nickname);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            if ($usuario = mysqli_fetch_assoc($resultado)) {
                if (password_verify($senha, $usuario['senha_hash'])) {
                    $_SESSION['logado'] = true;
                    $_SESSION['nickname'] = $usuario['nickname'];
                    $_SESSION['funcao'] = $usuario['funcao'];
                    mysqli_stmt_close($stmt);
                    mysqli_close($connect);
                    header("Location: index.php");
                    exit;
                } else {
                    $erro = "Senha incorreta.";
                }
            } else {
                $erro = "Usuário não encontrado.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $erro = "Erro na consulta ao banco.";
        }
    }
}





?>
----

<!DOCTYPE html>
<html>
<head>

    <title>Login</title>
      
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="bg-white d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <h2>Login</h2>
        <?php if ($erro): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>
        <form method="POST">
            <label>Nickname: <input type="text" name="nickname" required></label><br><br>
            <label>Senha: <input type="password" name="senha" required id="senha"></label><br><br>
            <button type="button" onclick="toggleSenha()">Mostrar</button>
</label><br><br>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
<script>
function toggleSenha() {
    const senhaInput = document.getElementById('senha');
    const btn = event.target;
    if (senhaInput.type === 'password') {
        senhaInput.type = 'text';
        btn.textContent = 'Ocultar';
    } else {
        senhaInput.type = 'password';
        btn.textContent = 'Mostrar';
    }
}
</script>
</html>
