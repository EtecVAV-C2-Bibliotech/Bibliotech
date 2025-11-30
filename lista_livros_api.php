<?php
require 'banco.php';

$sql = "SELECT * FROM livros ORDER BY titulo ASC";
$result = mysqli_query($connect, $sql);

$livros = [];
while ($l = mysqli_fetch_assoc($result)) {
    $livros[] = $l;
}

echo json_encode([
    "success" => true,
    "livros" => $livros
]);

?>
