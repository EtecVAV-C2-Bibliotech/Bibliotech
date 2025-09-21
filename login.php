<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
require 'banco.php';

$erro = '';
$tipo = 'funcionario'; // padrão

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = $_POST['nickname'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? 'funcionario'; // pega tipo enviado pelo formulário

    if (!$nickname || !$senha) {
        $erro = "Preencha usuário e senha.";
    } else {
        // Escolhe a tabela e a coluna de login conforme o tipo
        if($tipo === 'funcionario'){
            $tabela = 'funcionarios';
            $coluna_login = 'nickname';
        } else {
            $tabela = 'cliente';
            $coluna_login = 'Login';
        }

        $sql = "SELECT * FROM $tabela WHERE $coluna_login = ?";
        $stmt = mysqli_prepare($connect, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $nickname);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            if ($usuario = mysqli_fetch_assoc($resultado)) {
                if ($tipo === 'funcionario') {
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
                } else { // cliente
                    if ($senha === $usuario['Senha']) { // senha em texto puro
                        $_SESSION['logado'] = true;
                        $_SESSION['nickname'] = $usuario['Login'];
                        mysqli_stmt_close($stmt);
                        mysqli_close($connect);
                        header("Location: indexcli.php");
                        exit;
                    } else {
                        $erro = "Senha incorreta.";
                    }
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
<body class="hero login d-flex align-items-center justify-content-center vh-100">
    <div class="container m-5">
        <div class="row justify-content-center">
            <div class="col-6 col-sm-12 col-md-12 col-lg-8">
                <h2 class="mb-4 text-center">Login</h2>

                <!-- Botões para alternar tipo de login -->
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-primary me-2" onclick="mudarTipo('funcionario')">Funcionário</button>
                    <button type="button" class="btn btn-secondary" onclick="mudarTipo('cliente')">Cliente</button>
                </div>

                <?php if ($erro): ?>
                    <p class="erro text-danger text-center"><?= htmlspecialchars($erro) ?></p>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="tipo" id="tipoUsuario" value="<?= htmlspecialchars($tipo) ?>">
                    <div class="mb-3">
                        <label for="nickname" class="form-label">Nickname</label>
                        <input type="text" name="nickname" id="nickname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <div class="input-group">
                            <input type="password" name="senha" id="senha" class="form-control container-fluid" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleSenha()">Mostrar</button>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Entrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

function mudarTipo(tipo) {
    document.getElementById('tipoUsuario').value = tipo;

    const btnFunc = document.querySelector('button[onclick*="funcionario"]');
    const btnCli = document.querySelector('button[onclick*="cliente"]');

    if(tipo === 'funcionario') {
        btnFunc.classList.replace('btn-secondary','btn-primary');
        btnCli.classList.replace('btn-primary','btn-secondary');
    } else {
        btnCli.classList.replace('btn-secondary','btn-primary');
        btnFunc.classList.replace('btn-primary','btn-secondary');
    }
}
</script>
</body>
</html>
