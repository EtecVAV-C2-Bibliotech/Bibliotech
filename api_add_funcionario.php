<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");  // permite acessar do MIT App

require 'banco.php';

/* =====================
   FUNÇÃO DE VALIDAR SENHA
   (mesmas regras do seu sistema)
   ===================== */
function validarSenha($senha) {
    $temMaiuscula   = preg_match('/[A-Z]/', $senha);
    $temMinuscula   = preg_match('/[a-z]/', $senha);
    $temNumero      = preg_match('/[0-9]/', $senha);
    $temEspecial    = preg_match('/[^a-zA-Z0-9]/', $senha);
    $tamanhoValido  = strlen($senha) >= 6 && strlen($senha) <= 16;

    return $temMaiuscula && $temMinuscula && $temNumero && !$temEspecial && $tamanhoValido;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "erro" => "Método inválido"]);
    exit;
}

/* =====================
   PEGAR DADOS
   ===================== */
$nickname       = trim($_POST["nickname"] ?? "");
$senha          = $_POST["senha"] ?? "";
$cpf            = preg_replace("/[^0-9]/", "", $_POST["cpf"] ?? "");
$nome_completo  = trim($_POST["nome_completo"] ?? "");
$email          = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
$funcao         = strtolower($_POST["funcao"] ?? "");

/* =====================
   VALIDAÇÃO
   ===================== */
if (!$nickname || !$senha || !$nome_completo || !$email || !$funcao || !$cpf) {
    echo json_encode(["success" => false, "erro" => "Preencha todos os campos."]);
    exit;
}

if (!validarSenha($senha)) {
    echo json_encode([
        "success" => false,
        "erro" => "A senha deve conter maiúscula, minúscula, número, sem especiais e entre 6 e 16 caracteres."
    ]);
    exit;
}

/* =====================
   CHECAR SE CPF OU NICK EXISTEM
   ===================== */
$sql_check = "SELECT idFunc FROM funcionarios WHERE cpf = ? OR nickname = ?";
$stmt = mysqli_prepare($connect, $sql_check);
mysqli_stmt_bind_param($stmt, "ss", $cpf, $nickname);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo json_encode(["success" => false, "erro" => "CPF ou usuário já cadastrados."]);
    exit;
}
mysqli_stmt_close($stmt);

/* =====================
   INSERIR NO BANCO
   ===================== */
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO funcionarios (nickname, senha_hash, nome_completo, email, funcao, cpf)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, "ssssss",
    $nickname, $senha_hash, $nome_completo, $email, $funcao, $cpf
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true, "mensagem" => "Funcionário cadastrado!"]);
} else {
    echo json_encode(["success" => false, "erro" => mysqli_error($connect)]);
}

mysqli_stmt_close($stmt);
?>
