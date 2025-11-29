<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if (!isset($_SESSION['logado']) || $_SESSION['funcao'] !== 'gerente') {
    header("Location: login.php");
    exit;
}

require 'banco.php';

$dadosFuncionario = null;
$mensagem = "";
$acao = $_POST['acao'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idFunc = $_POST['id'];
    
    if (!empty($acao)) {
        if ($acao === 'nome' && isset($_POST['novo_nome'])) {
            $novo_nome = trim($_POST['novo_nome']);
            $sql = "UPDATE funcionarios SET nome_completo = ? WHERE idFunc = ?";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "si", $novo_nome, $idFunc);
            mysqli_stmt_execute($stmt);
            $mensagem = "<p class='sucesso'>Nome atualizado com sucesso!</p>";
        } else if ($acao === 'email' && isset($_POST['novo_email'])) {
            $novo_email = trim($_POST['novo_email']);
            $sql = "UPDATE funcionarios SET email = ? WHERE idFunc = ?";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "si", $novo_email, $idFunc);
            mysqli_stmt_execute($stmt);
            $mensagem = "<p class='sucesso'>E-mail atualizado com sucesso!</p>";
        } else if ($acao === 'nickname' && isset($_POST['novo_nick'])) {
            $novo_nick = trim($_POST['novo_nick']);
            $sql = "UPDATE funcionarios SET nickname = ? WHERE idFunc = ?";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "si", $novo_nick, $idFunc);
            mysqli_stmt_execute($stmt);
            $mensagem = "<p class='sucesso'>Nickname atualizado com sucesso!</p>";
        } else if ($acao === 'funcao' && isset($_POST['nova_funcao'])) {
            $nova_funcao = $_POST['nova_funcao'];
            $sql = "UPDATE funcionarios SET funcao = ? WHERE idFunc = ?";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "si", $nova_funcao, $idFunc);
            mysqli_stmt_execute($stmt);
            $mensagem = "<p class='sucesso'>Função atualizada com sucesso!</p>";
        }
    }
    
    $sql = "SELECT * FROM funcionarios WHERE idFunc = ?";
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idFunc);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $dadosFuncionario = mysqli_fetch_assoc($resultado);

    if (!$dadosFuncionario) {
        $mensagem = "<p class='erro'>Funcionário não encontrado.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Gerente</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="hero paniel  d-flex align-items-center justify-content-center vh-100">
<div class="container">
    <h1>Painel de Configurações</h1>
    <p>Bem-vindo, <?= htmlspecialchars($_SESSION['nickname']); ?>!</p>

    <form method="POST">
        <h3>Buscar funcionário</h3>
        <label>ID do funcionário:
            <input type="number" name="id" required>
        </label>
        <button type="submit">Buscar</button>
    </form>

    <?= $mensagem ?>

    <?php if ($dadosFuncionario): ?>
        <h3>Dados do Funcionário:</h3>
        <ul>
            <li><strong>ID:</strong> <?= htmlspecialchars($dadosFuncionario['idFunc']) ?></li>
            <li><strong>Nome:</strong> <?= htmlspecialchars($dadosFuncionario['nome_completo']) ?></li>
            <li><strong>Nickname:</strong> <?= htmlspecialchars($dadosFuncionario['nickname']) ?></li>
            <li><strong>Email:</strong> <?= htmlspecialchars($dadosFuncionario['email']) ?></li>
            <li><strong>Função:</strong> <?= htmlspecialchars($dadosFuncionario['funcao']) ?></li>
        </ul>

        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($dadosFuncionario['idFunc']) ?>">
            <label>Alterar Nome:
                <input type="text" name="novo_nome" placeholder="Novo nome" required>
            </label>
            <button type="submit" name="acao" value="nome">Salvar nome</button>
        </form>

        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($dadosFuncionario['idFunc']) ?>">
            <label>Alterar Nickname:
                <input type="text" name="novo_nick" placeholder="Novo nickname" required>
            </label>
            <button type="submit" name="acao" value="nickname">Salvar nickname</button>
        </form>

        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($dadosFuncionario['idFunc']) ?>">
            <label>Alterar Email:
                <input type="email" name="novo_email" placeholder="Novo email" required>
            </label>
            <button type="submit" name="acao" value="email">Salvar email</button>
        </form>

        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($dadosFuncionario['idFunc']) ?>">
            <label>Alterar Função:
                <select name="nova_funcao" required>
                    <option value="gerente" <?= $dadosFuncionario['funcao'] === 'gerente' ? 'selected' : '' ?>>Gerente</option>
                    <option value="repositor" <?= $dadosFuncionario['funcao'] === 'repositor' ? 'selected' : '' ?>>Repositor</option>
                </select>
            </label>
            <button type="submit" name="acao" value="funcao">Salvar função</button>
        </form>
    <?php endif; ?>

    <br><a href="index.php">Voltar</a> | <a href="logout.php">Sair</a>
</div>
</body>
</html>

<?php mysqli_close($connect); ?>
