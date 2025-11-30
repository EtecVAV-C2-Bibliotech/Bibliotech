<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require 'banco.php';

$titulo = trim($_POST['titulo'] ?? '');
$autor = trim($_POST['autor'] ?? '');
$editora = trim($_POST['editora'] ?? '');
$ano = intval($_POST['ano'] ?? 0);
$quantidade = intval($_POST['quantidade'] ?? 0);

if (!$titulo || !$autor || $quantidade <= 0) {
    echo json_encode(["success" => false, "erro" => "Preencha Título, Autor e Quantidade"]);
    exit;
}

$sql = "INSERT INTO livros (titulo, autor, editora, ano_publicacao, quantidade) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, "ssssi", 
    $titulo, $autor, $editora, $ano, $quantidade
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true, "mensagem" => "Livro cadastrado com sucesso"]);
} else {
    echo json_encode(["success" => false, "erro" => mysqli_error($connect)]);
}
mysqli_stmt_close($stmt);
?>