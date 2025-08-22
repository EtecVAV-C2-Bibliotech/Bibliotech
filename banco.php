<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
    header("Location: login.php");
    exit;
} 

$servername = "localhost";
$username = "root";
$password = "";
$database = "biblioteca_funcionarios";

$connect = mysqli_connect($servername, $username, $password, $database);

if (!$connect) {
    die("Erro na conexão: " . mysqli_connect_error());
}
?>
