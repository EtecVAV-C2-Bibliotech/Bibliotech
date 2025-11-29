<?php
if(!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

require 'banco.php';

$sessaoSelecionada = $_GET['sessao'] ?? '';

// Buscar sessões
$sessoes = [];
$sql = "SELECT DISTINCT sessao FROM livros ORDER BY sessao ASC";
$result = mysqli_query($connect, $sql);
if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $sessoes[] = $row['sessao'];
    }
}

// Buscar livros da sessão
$livros = [];
if ($sessaoSelecionada) {
    $stmt = mysqli_prepare($connect, "SELECT * FROM livros WHERE sessao = ?");
    mysqli_stmt_bind_param($stmt, "s", $sessaoSelecionada);
    mysqli_stmt_execute($stmt);
    $resultadoLivros = mysqli_stmt_get_result($stmt);

    while($row = mysqli_fetch_assoc($resultadoLivros)) {
        $livros[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Livros Disponíveis</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/estilo.css">
</head>
<body class="hero inicio d-flex align-items-start justify-content-center vh-100" style="padding-top:40px;">
<div class="container">
    <h1>Seções de Livros</h1>
    <p>Escolha uma sessão para visualizar os livros:</p>

    <div class="mb-4">
        <?php foreach($sessoes as $sessao): ?>
            <a href="livros.php?sessao=<?= urlencode($sessao) ?>" class="btn btn-secondary me-2 mb-2">
                <?= htmlspecialchars($sessao) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if($sessaoSelecionada): ?>
        <h2>Livros na sessão: <?= htmlspecialchars($sessaoSelecionada) ?></h2>

        <?php if(count($livros) > 0): ?>
            <div class="row">
                <?php foreach($livros as $livro): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card" style="min-height: 420px;">

                            <?php if (!empty($livro['imagem'])): ?>
                                <img src="uploads_livros/<?= htmlspecialchars($livro['imagem']) ?>" class="card-img-top" style="height:250px; object-fit:cover;">
                            <?php else: ?>
                                <img src="img/sem_capa.png" class="card-img-top" style="height:250px; object-fit:cover;">
                            <?php endif; ?>

                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($livro['titulo']) ?></h5>
                                <p class="card-text"><strong>Autor:</strong> <?= htmlspecialchars($livro['autor']) ?></p>
                                <p class="card-text"><strong>Editora:</strong> <?= htmlspecialchars($livro['editora']) ?></p>
                                <p class="card-text"><strong>Ano:</strong> <?= htmlspecialchars($livro['ano_publicacao']) ?></p>
                                <p class="card-text"><strong>Quantidade:</strong> <?= htmlspecialchars($livro['quantidade']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="erro">Não há livros cadastrados nessa sessão.</p>
        <?php endif; ?>
    <?php endif; ?>

    <br><a href="indexcli.php">Voltar</a> | <a href="logout.php">Sair</a>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($connect); ?>