<?php
session_start();
require "conexao.php";

if (!isset($_SESSION['logado']) || $_SESSION['funcao'] !== 'gerente') {
    header("Location: login.php");
    exit;
}

$sql = "
    SELECT c.idCompra, l.titulo, f.nickname AS cliente,
           c.quantidade, c.valor_unitario, c.data_compra
    FROM compras c
    JOIN livros l ON c.idLivro = l.idLivro
    JOIN funcionarios f ON c.idCliente = f.idFunc
    ORDER BY c.data_compra DESC
";
$compras = $conexao->query($sql)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Compras</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="container mt-5">
<h3>Histórico de Compras</h3>

<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Livro</th>
            <th>Cliente</th>
            <th>Qtd</th>
            <th>Valor Unit.</th>
            <th>Data</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($compras as $c): ?>
        <tr>
            <td><?= $c['idCompra'] ?></td>
            <td><?= htmlspecialchars($c['titulo']) ?></td>
            <td><?= htmlspecialchars($c['cliente']) ?></td>
            <td><?= $c['quantidade'] ?></td>
            <td>R$ <?= number_format($c['valor_unitario'],2,',','.') ?></td>
            <td><?= $c['data_compra'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
