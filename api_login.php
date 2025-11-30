<?php
require "banco.php";

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "error" => "Use método POST."
    ]);
    exit;
}

$nickname = $_POST["nickname"] ?? '';
$senha    = $_POST["senha"] ?? '';

if (!$nickname || !$senha) {
    echo json_encode([
        "success" => false,
        "error" => "Preencha nickname e senha."
    ]);
    exit;
}

$sql = "SELECT * FROM funcionarios WHERE nickname = ?";
$stmt = mysqli_prepare($connect, $sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "error" => "Erro na consulta."
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $nickname);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($usuario = mysqli_fetch_assoc($resultado)) {

    if (password_verify($senha, $usuario["senha_hash"])) {

        // 🔥 AQUI: verifica se é gerente
        if ($usuario["funcao"] !== "gerente") {
            echo json_encode([
                "success" => false,
                "error" => "Usuário não é gerente."
            ]);
            exit;
        }

        // Login válido + é gerente
        echo json_encode([
            "success" => true,
            "idFunc"  => $usuario["idFunc"],
            "nickname" => $usuario["nickname"],
            "funcao" => $usuario["funcao"]
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "error" => "Senha incorreta."
        ]);

    }

} else {

    echo json_encode([
        "success" => false,
        "error" => "Usuário não encontrado."
    ]);

}

mysqli_stmt_close($stmt);
mysqli_close($connect);
?>
