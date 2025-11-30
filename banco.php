<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "biblioteca_funcionarios";

$connect = mysqli_connect($servername, $username, $password, $database);

if (!$connect) {
    
    die("Erro na conexão: " . mysqli_connect_error());
}
function sendResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}
?>