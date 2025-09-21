<?php
session_start();
require 'banco.php';

// Redireciona se não estiver logado
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

// Atualiza status de atrasados
$connect->query("UPDATE emprestimos 
                 SET status = 'atrasado' 
                 WHERE status = 'ativo' AND data_prevista < CURDATE()");

// Devolver livro
if (isset($_GET['devolver'])) {
    $idEmprestimo = (int)$_GET['devolver'];
    $stmt = $connect->prepare("SELECT idLivro FROM emprestimos WHERE idEmprestimo = ?");
    $stmt->bind_param("i", $idEmprestimo);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $idLivro = $row['idLivro'];

        $stmt2 = $connect->prepare("UPDATE emprestimos SET status='devolvido', data_devolucao=CURDATE() WHERE idEmprestimo=?");
        $stmt2->bind_param("i", $idEmprestimo);
        $stmt2->execute();
        $stmt2->close();

        $stmt3 = $connect->prepare("UPDATE livros SET quantidade = quantidade + 1 WHERE idLivro=?");
        $stmt3->bind_param("i", $idLivro);
        $stmt3->execute();
        $stmt3->close();

        $mensagem = "📘 Livro devolvido com sucesso!";
    }
    $stmt->close();
}

// Registrar novo empréstimo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idFunc'], $_POST['idLivro'], $_POST['data_prevista'], $_POST['idCliente'])) {
        $idFunc = (int)$_POST['idFunc'];
        $idLivro = (int)$_POST['idLivro'];
        $dataPrevista = $_POST['data_prevista'];
        $idCliente = (int)$_POST['idCliente'];

        $stmt = $connect->prepare("INSERT INTO emprestimos (idFunc, idCliente, idLivro, data_emprestimo, data_prevista, status) 
                                   VALUES (?, ?, ?, CURDATE(), ?, 'ativo')");
        $stmt->bind_param("iiis", $idFunc, $idCliente, $idLivro, $dataPrevista);
        if ($stmt->execute()) {
            $stmt2 = $connect->prepare("UPDATE livros SET quantidade = quantidade - 1 WHERE idLivro=?");
            $stmt2->bind_param("i", $idLivro);
            $stmt2->execute();
            $stmt2->close();
            $mensagem = "✅ Empréstimo registrado com sucesso!";
        } else {
            $erro = "Erro: " . $stmt->error;
        }
        $stmt->close();
    }

    // Completar solicitações pendentes
    if (isset($_POST['completar_solicitacao'], $_POST['idEmprestimo'], $_POST['idFunc'], $_POST['data_prevista'])) {
        $idEmp = (int)$_POST['idEmprestimo'];
        $idFunc = (int)$_POST['idFunc'];
        $dataPrev = $_POST['data_prevista'];

        $stmt = $connect->prepare("UPDATE emprestimos SET idFunc=?, data_prevista=?, status='ativo' WHERE idEmprestimo=?");
        $stmt->bind_param("isi", $idFunc, $dataPrev, $idEmp);
        if ($stmt->execute()) {
            $stmt2 = $connect->prepare("SELECT idLivro FROM emprestimos WHERE idEmprestimo=?");
            $stmt2->bind_param("i", $idEmp);
            $stmt2->execute();
            $res = $stmt2->get_result();
            $idLivro = $res->fetch_assoc()['idLivro'];
            $stmt2->close();

            $stmt3 = $connect->prepare("UPDATE livros SET quantidade = quantidade - 1 WHERE idLivro=?");
            $stmt3->bind_param("i", $idLivro);
            $stmt3->execute();
            $stmt3->close();

            $mensagem = "✅ Solicitação completada!";
        } else {
            $erro = "Erro: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Pegar funcionários e clientes
$funcionarios = $connect->query("SELECT idFunc, nome_completo FROM funcionarios WHERE funcao!='cliente'");
$clientes = $connect->query("SELECT idFunc, nome_completo FROM funcionarios WHERE funcao='cliente'");
$livros = $connect->query("SELECT idLivro, titulo, quantidade FROM livros");

// Empréstimos ativos
$emprestimos = $connect->query("
    SELECT e.idEmprestimo, f.nome_completo AS funcionario, c.nome_completo AS cliente, l.titulo, 
           e.data_emprestimo, e.data_prevista, e.data_devolucao, e.status
    FROM emprestimos e
    JOIN funcionarios f ON e.idFunc = f.idFunc
    JOIN funcionarios c ON e.idCliente = c.idFunc
    JOIN livros l ON e.idLivro = l.idLivro
    ORDER BY e.idEmprestimo DESC
");

// Solicitações pendentes
$solicitacoes = $connect->query("
    SELECT e.idEmprestimo, c.nome_completo AS cliente, l.titulo
    FROM emprestimos e
    JOIN funcionarios c ON e.idCliente = c.idFunc
    JOIN livros l ON e.idLivro = l.idLivro
    WHERE e.status='solicitado'
");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Empréstimos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/estilo.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
    body.hero { padding-top: 20px; background-color: #f8f9fa; }
    .card-header { font-weight: bold; }
    .table td, .table th { vertical-align: middle; }
</style>
</head>
<body class="hero emprestimo">
<div class="container">

    <h1 class="mb-4 text-center">📚 Empréstimos de Livros</h1>

    <?php if (!empty($mensagem)): ?>
        <div class="alert alert-success"><?= $mensagem ?></div>
    <?php endif; ?>
    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Formulário de Novo Empréstimo -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Registrar Novo Empréstimo</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Funcionário (responsável)</label>
                            <select name="idFunc" class="form-select" required>
                                <option value="">Selecione</option>
                                <?php while ($f = $funcionarios->fetch_assoc()): ?>
                                    <option value="<?= $f['idFunc'] ?>"><?= htmlspecialchars($f['nome_completo']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cliente</label>
                            <select name="idCliente" class="form-select" required>
                                <option value="">Selecione</option>
                                <?php while ($c = $clientes->fetch_assoc()): ?>
                                    <option value="<?= $c['idFunc'] ?>"><?= htmlspecialchars($c['nome_completo']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Livro</label>
                            <select name="idLivro" class="form-select" required>
                                <option value="">Selecione</option>
                                <?php while ($l = $livros->fetch_assoc()): ?>
                                    <option value="<?= $l['idLivro'] ?>" <?= ($l['quantidade'] <= 0 ? 'disabled' : '') ?>>
                                        <?= htmlspecialchars($l['titulo']) ?> (<?= $l['quantidade'] ?> disponíveis)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data de Devolução Prevista</label>
                            <input type="date" name="data_prevista" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Emprestar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Solicitações Pendentes -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">Solicitações Pendentes</div>
                <div class="card-body">
                    <?php if ($solicitacoes->num_rows > 0): ?>
                        <?php while ($s = $solicitacoes->fetch_assoc()): ?>
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="idEmprestimo" value="<?= $s['idEmprestimo'] ?>">
                            <div class="mb-2"><strong>Cliente:</strong> <?= htmlspecialchars($s['cliente']) ?></div>
                            <div class="mb-2"><strong>Livro:</strong> <?= htmlspecialchars($s['titulo']) ?></div>
                            <div class="mb-3">
                                <label class="form-label">Funcionário responsável</label>
                                <select name="idFunc" class="form-select" required>
                                    <option value="">Selecione</option>
                                    <?php
                                    $funcionarios2 = $connect->query("SELECT idFunc, nome_completo FROM funcionarios WHERE funcao!='cliente'");
                                    while ($f2 = $funcionarios2->fetch_assoc()):
                                    ?>
                                        <option value="<?= $f2['idFunc'] ?>"><?= htmlspecialchars($f2['nome_completo']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Data de Devolução Prevista</label>
                                <input type="date" name="data_prevista" class="form-control" required>
                            </div>
                            <button type="submit" name="completar_solicitacao" class="btn btn-warning w-100">Completar Solicitação</button>
                        </form>
                        <hr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center">Não há solicitações pendentes.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Empréstimos -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">Empréstimos Registrados</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Funcionário</th>
                        <th>Cliente</th>
                        <th>Livro</th>
                        <th>Data Empréstimo</th>
                        <th>Data Prevista</th>
                        <th>Data Devolução</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($e = $emprestimos->fetch_assoc()): ?>
                    <tr>
                        <td><?= $e['idEmprestimo'] ?></td>
                        <td><?= htmlspecialchars($e['funcionario']) ?></td>
                        <td><?= htmlspecialchars($e['cliente']) ?></td>
                        <td><?= htmlspecialchars($e['titulo']) ?></td>
                        <td><?= $e['data_emprestimo'] ?></td>
                        <td><?= $e['data_prevista'] ?></td>
                        <td><?= $e['data_devolucao'] ?? '-' ?></td>
                        <td style="color: <?= $e['status']=='atrasado'?'red':($e['status']=='ativo'?'orange':'green') ?>;">
                            <?= $e['status'] ?>
                        </td>
                        <td>
                            <?php if ($e['status'] == 'ativo' || $e['status'] == 'atrasado'): ?>
                                <a href="?devolver=<?= $e['idEmprestimo'] ?>" class="btn btn-sm btn-danger">Devolver</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <a href="index.php" class="btn btn-secondary mb-4">Voltar</a>

</div>
</body>
</html>
