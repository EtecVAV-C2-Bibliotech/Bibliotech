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
                    $_SESSION['idFunc'] = $usuario['idFunc'];
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


<!DOCTYPE html>
<html>
<head>

    <title>Login</title>
      
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="hero login d-flex align-items-center justify-content-center vh-100   ">
     <div class="container m-5">
        <div class="row justify-content-center">
            <div class="col-6 col-sm-12 col-md-12 col-lg-8">
               
              
                    <h2 class="mb-4 text-center">Login</h2>
                    <?php if ($erro): ?>
                        <p class="erro text-danger text-center"><?= htmlspecialchars($erro) ?></p>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="nickname" class="form-label">Nickname</label>
                            <input type="text" name="nickname" id="nickname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                
                            <div class="input-group ">
                                <input type="password" name="senha" id="senha" class="form-control container-fluid" required>
                                <button type="button" class=" btn-outline-secondary " onclick="toggleSenha()">Mostrar</button>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" >Entrar</button>
                        </div>
                        <div class="d-grid mt-3">
                            <a href="cadastrarcli.php" class="btn btn-outline-primary">Cadastre-se</a>
                        </div>
                    </form>
                </div>
        </div>
            </div>
        </div>
        </div>
     </div>
        </div>
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