<?php
require 'banco.php';

$id = intval($_POST["id"]);
$titulo = $_POST["titulo"];
$autor = $_POST["autor"];
$editora = $_POST["editora"];
$ano = intval($_POST["ano"]);
$quantidade = intval($_POST["quantidade"]);

$sql = "UPDATE livros SET titulo=?, autor=?, editora=?, ano_publicacao=?, quantidade=? WHERE idLivro=?";
$stmt = mysqli_prepare($connect, $sql);

mysqli_stmt_bind_param($stmt, "sssiii", $titulo, $autor, $editora, $ano, $quantidade, $id);

if(mysqli_stmt_execute($stmt)){
    echo json_encode(["success"=>true]);
} else {
    echo json_encode(["success"=>false, "error"=>mysqli_error($connect)]);
}

?>
