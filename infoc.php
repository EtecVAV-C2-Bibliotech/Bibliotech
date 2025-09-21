<?php
if(!isset($_SESSION)) { session_start(); } 

if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

require 'banco.php';

$mensagem = "";

// Pega os dados do usuário pelo nickname salvo na sessão
$nickname = $_SESSION['nickname'];
$sql = "SELECT * FROM funcionarios WHERE nickname = ?";
$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, "s", $nickname);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);
$idUsuario = $usuario['idFunc'] ?? null;
mysqli_stmt_close($stmt);

// Se enviou alteração
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $idUsuario) {
    if (isset($_POST['novo_nome'])) {
        $novo_nome = trim($_POST['novo_nome']);
        $stmt = mysqli_prepare($connect, "UPDATE funcionarios SET nome_completo = ? WHERE idFunc = ?");
        mysqli_stmt_bind_param($stmt, "si", $novo_nome, $idUsuario);
        mysqli_stmt_execute($stmt);
        $mensagem = "Nome atualizado com sucesso!";
        mysqli_stmt_close($stmt);
    } 
    if (isset($_POST['novo_login'])) {
        $novo_login = trim($_POST['novo_login']);
        $stmt = mysqli_prepare($connect, "UPDATE funcionarios SET nickname = ? WHERE idFunc = ?");
        mysqli_stmt_bind_param($stmt, "si", $novo_login, $idUsuario);
        mysqli_stmt_execute($stmt);
        $_SESSION['nickname'] = $novo_login; // atualiza a sessão
        $mensagem = "Login atualizado com sucesso!";
        mysqli_stmt_close($stmt);
    } 
    if (isset($_POST['novo_email'])) {
        $novo_email = trim($_POST['novo_email']);
        $stmt = mysqli_prepare($connect, "UPDATE funcionarios SET email = ? WHERE idFunc = ?");
        mysqli_stmt_bind_param($stmt, "si", $novo_email, $idUsuario);
        mysqli_stmt_execute($stmt);
        $mensagem = "E-mail atualizado com sucesso!";
        mysqli_stmt_close($stmt);
    } 
    if (isset($_POST['nova_senha'])) {
        $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($connect, "UPDATE funcionarios SET senha_hash = ? WHERE idFunc = ?");
        mysqli_stmt_bind_param($stmt, "si", $nova_senha, $idUsuario);
        mysqli_stmt_execute($stmt);
        $mensagem = "Senha atualizada com sucesso!";
        mysqli_stmt_close($stmt);
    }

    // Atualiza os dados depois da mudança
    $stmt = mysqli_prepare($connect, "SELECT * FROM funcionarios WHERE idFunc = ?");
    mysqli_stmt_bind_param($stmt, "i", $idUsuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Minhas Informações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
        <h2 class="text-center mb-4">Minhas Informações</h2>
        <p class="text-center">Bem-vindo, <strong><?= htmlspecialchars($_SESSION['nickname']); ?></strong>!</p>

        <?php if ($mensagem): ?>
            <div class="alert alert-success text-center"><?= $mensagem ?></div>
        <?php endif; ?>

        <?php if ($usuario): ?>
            <form method="POST" class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" class="form-control" name="novo_nome" value="<?= htmlspecialchars($usuario['nome_completo']) ?>" required>
                <button type="submit" class="btn btn-primary w-100 mt-2">Salvar Nome</button>
            </form>

            <form method="POST" class="mb-3">
                <label class="form-label">Login</label>
                <input type="text" class="form-control" name="novo_login" value="<?= htmlspecialchars($usuario['nickname']) ?>" required>
                <button type="submit" class="btn btn-primary w-100 mt-2">Salvar Login</button>
            </form>

            <form method="POST" class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" class="form-control" name="novo_email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                <button type="submit" class="btn btn-primary w-100 mt-2">Salvar E-mail</button>
            </form>

            <form method="POST" class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" class="form-control" name="nova_senha" placeholder="Digite nova senha" required>
                <button type="submit" class="btn btn-danger w-100 mt-2">Salvar Senha</button>
            </form>
        <?php endif; ?>

        <div class="d-flex justify-content-between mt-3">
            <a href="indexcli.php" class="btn btn-secondary">Voltar</a>
            <a href="logout.php" class="btn btn-outline-danger">Sair</a>
        </div>
    </div>
</div>
</body>
</html>
<?php mysqli_close($connect); ?>
