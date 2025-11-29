<?php
session_start();
require 'banco.php';

// Redireciona se não estiver logado
if (!isset($_SESSION['logado']) || empty($_SESSION['idFunc'])) {
    header("Location: login.php");
    exit;
}

// Pega o id do usuário logado (cliente)
$idCliente = $_SESSION['idFunc'];

// Atualiza status de atrasado automaticamente
$connect->query("
    UPDATE emprestimos 
    SET status = 'atrasado' 
    WHERE status = 'ativo' AND data_prevista < CURDATE()
");

// Busca os empréstimos do cliente logado
$stmt = mysqli_prepare($connect, "
    SELECT e.idEmprestimo, l.titulo, e.data_emprestimo, e.data_prevista, e.data_devolucao, e.status
    FROM emprestimos e
    JOIN livros l ON e.idLivro = l.idLivro
    WHERE e.idCliente = ?
    ORDER BY e.idEmprestimo DESC
");
mysqli_stmt_bind_param($stmt, "i", $idCliente);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$emprestimos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $emprestimos[] = $row;
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meus Empréstimos</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        .status-ativo { color: orange; font-weight: bold; }
        .status-atrasado { color: red; font-weight: bold; }
        .status-devolvido { color: green; font-weight: bold; }
        .table th, .table td { vertical-align: middle; }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">Meus Empréstimos</h1>

    <?php if (count($emprestimos) === 0): ?>
        <div class="alert alert-info">Você ainda não possui empréstimos.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Livro</th>
                        <th>Data do Empréstimo</th>
                        <th>Data Prevista</th>
                        <th>Data de Devolução</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emprestimos as $e): ?>
                        <tr>
                            <td><?= $e['idEmprestimo'] ?></td>
                            <td><?= htmlspecialchars($e['titulo']) ?></td>
                            <td><?= $e['data_emprestimo'] ?></td>
                            <td><?= $e['data_prevista'] ?></td>
                            <td><?= $e['data_devolucao'] ?? '-' ?></td>
                            <td class="<?= 'status-' . $e['status'] ?>">
                                <?= ucfirst($e['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="indexcli.php" class="btn btn-secondary">Voltar</a>
        <a href="logout.php" class="btn btn-danger">Sair</a>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php mysqli_close($connect); ?>
