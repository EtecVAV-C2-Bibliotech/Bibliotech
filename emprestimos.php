<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "biblioteca_funcionarios";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}


if (isset($_GET['devolver'])) {
    $idEmprestimo = $_GET['devolver'];
    $res = $conn->query("SELECT idLivro FROM emprestimos WHERE idEmprestimo = $idEmprestimo");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $idLivro = $row['idLivro'];
        $conn->query("UPDATE emprestimos 
                      SET status = 'devolvido', data_devolucao = CURDATE() 
                      WHERE idEmprestimo = $idEmprestimo");
        $conn->query("UPDATE livros SET quantidade = quantidade + 1 WHERE idLivro = $idLivro");
        $mensagem = "📘 Livro devolvido com sucesso!";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['idFunc'], $_POST['idLivro'], $_POST['data_prevista'])) {
    $idFunc = $_POST['idFunc'];
    $idLivro = $_POST['idLivro'];
    $dataPrevista = $_POST['data_prevista'];

    $sql = "INSERT INTO emprestimos (idFunc, idLivro, data_emprestimo, data_prevista) 
            VALUES ('$idFunc', '$idLivro', CURDATE(), '$dataPrevista')";

    if ($conn->query($sql) === TRUE) {
        $conn->query("UPDATE livros SET quantidade = quantidade - 1 WHERE idLivro = '$idLivro'");
        $mensagem = "✅ Empréstimo registrado com sucesso!";
    } else {
        $erro = "Erro: " . $conn->error;
    }
}

$conn->query("UPDATE emprestimos 
              SET status = 'atrasado' 
              WHERE status = 'ativo' AND data_prevista < CURDATE()");

$funcionarios = $conn->query("SELECT idFunc, nome_completo FROM funcionarios");
$livros = $conn->query("SELECT idLivro, titulo, quantidade FROM livros");
$emprestimos = $conn->query("
    SELECT e.idEmprestimo, f.nome_completo, l.titulo, e.data_emprestimo, 
           e.data_prevista, e.data_devolucao, e.status
    FROM emprestimos e
    JOIN funcionarios f ON e.idFunc = f.idFunc
    JOIN livros l ON e.idLivro = l.idLivro
    ORDER BY e.idEmprestimo DESC
");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <title>Empréstimos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="hero emprestimo">
<div class="container">
    <h1>Empréstimos de Livros</h1>

    <?php if (!empty($mensagem)) echo "<p class='sucesso'>$mensagem</p>"; ?>
    <?php if (!empty($erro)) echo "<p class='erro'>$erro</p>"; ?>

    <h3>Registrar Novo Empréstimo</h3>
    <form method="POST">
        <label>Funcionário:</label>
        <select name="idFunc" required>
            <option value="">Selecione</option>
            <?php while ($f = $funcionarios->fetch_assoc()) { ?>
                <option value="<?= $f['idFunc'] ?>"><?= $f['nome_completo'] ?></option>
            <?php } ?>
        </select>

        <label>Livro:</label>
        <select name="idLivro" required>
            <option value="">Selecione</option>
            <?php while ($l = $livros->fetch_assoc()) { ?>
                <option value="<?= $l['idLivro'] ?>" <?= ($l['quantidade'] <= 0 ? 'disabled' : '') ?>>
                    <?= $l['titulo'] ?> (<?= $l['quantidade'] ?> disponíveis)
                </option>
            <?php } ?>
        </select>

        <label>Data de Devolução Prevista:</label>
        <input type="date" name="data_prevista" required>

        <button type="submit">Emprestar</button>
    </form>

    <h3>Empréstimos Registrados</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Funcionário</th>
            <th>Livro</th>
            <th>Data Empréstimo</th>
            <th>Data Prevista</th>
            <th>Data Devolução</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
        <?php while ($e = $emprestimos->fetch_assoc()) { ?>
        <tr>
            <td><?= $e['idEmprestimo'] ?></td>
            <td><?= $e['nome_completo'] ?></td>
            <td><?= $e['titulo'] ?></td>
            <td><?= $e['data_emprestimo'] ?></td>
            <td><?= $e['data_prevista'] ?></td>
            <td><?= $e['data_devolucao'] ?? '-' ?></td>
            <td style="color: <?= $e['status']=='atrasado'?'red':($e['status']=='ativo'?'orange':'green') ?>;">
                <?= $e['status'] ?>
            </td>
            <td>
                <?php if ($e['status'] == 'ativo' || $e['status'] == 'atrasado') { ?>
                    <a href="?devolver=<?= $e['idEmprestimo'] ?>" onclick="return confirm('Confirmar devolução?')">Devolver</a>
                <?php } else { ?>
                    -
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
    <br><a href="index.php">Voltar</a>
</div>
</body>
</html>