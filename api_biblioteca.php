<?php
// api_biblioteca.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require 'banco.php';

// Função para enviar resposta JSON
function sendResponse($success, $message = '', $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Função para validar dados básicos
function validateInput($data, $requiredFields) {
    foreach ($requiredFields as $field) {
        if (empty(trim($data[$field] ?? ''))) {
            return "Campo '$field' é obrigatório";
        }
    }
    return null;
}

// Roteamento básico
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        
        // LOGIN
        case 'login':
            if ($method !== 'POST') {
                sendResponse(false, 'Método não permitido');
            }
            
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $error = validateInput($input, ['nickname', 'senha']);
            if ($error) {
                sendResponse(false, $error);
            }
            
            $nickname = trim($input['nickname']);
            $senha = $input['senha'];
            
            $sql = "SELECT idFunc, nickname, senha_hash, nome_completo, funcao FROM funcionarios WHERE nickname = ?";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "s", $nickname);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            
            if ($usuario = mysqli_fetch_assoc($resultado)) {
                if (password_verify($senha, $usuario['senha_hash'])) {
                    sendResponse(true, 'Login realizado com sucesso', [
                        'id' => $usuario['idFunc'],
                        'nickname' => $usuario['nickname'],
                        'nome_completo' => $usuario['nome_completo'],
                        'funcao' => $usuario['funcao']
                    ]);
                } else {
                    sendResponse(false, 'Senha incorreta');
                }
            } else {
                sendResponse(false, 'Usuário não encontrado');
            }
            
            mysqli_stmt_close($stmt);
            break;
            
        // CADASTRAR LIVRO (apenas gerentes)
        case 'cadastrar_livro':
            if ($method !== 'POST') {
                sendResponse(false, 'Método não permitido');
            }
            
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            // Verificar se é gerente (token simples)
            $token = $input['token'] ?? '';
            if (!$token) {
                sendResponse(false, 'Token de autenticação necessário');
            }
            
            // Verificar se o token corresponde a um gerente
            $sql_check = "SELECT idFunc FROM funcionarios WHERE idFunc = ? AND funcao = 'gerente'";
            $stmt_check = mysqli_prepare($connect, $sql_check);
            mysqli_stmt_bind_param($stmt_check, "i", $token);
            mysqli_stmt_execute($stmt_check);
            $result_check = mysqli_stmt_get_result($stmt_check);
            
            if (!mysqli_fetch_assoc($result_check)) {
                sendResponse(false, 'Acesso negado. Apenas gerentes podem cadastrar livros.');
            }
            mysqli_stmt_close($stmt_check);
            
            $error = validateInput($input, ['titulo', 'autor', 'quantidade']);
            if ($error) {
                sendResponse(false, $error);
            }
            
            $titulo = trim($input['titulo']);
            $autor = trim($input['autor']);
            $sessao = trim($input['sessao'] ?? 'Geral');
            $editora = trim($input['editora'] ?? '');
            $ano = intval($input['ano'] ?? 0);
            $quantidade = intval($input['quantidade']);
            
            if ($quantidade <= 0) {
                sendResponse(false, 'Quantidade deve ser maior que zero');
            }
            
            $sql = "INSERT INTO livros (titulo, autor, sessao, editora, ano_publicacao, quantidade) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "ssssii", $titulo, $autor, $sessao, $editora, $ano, $quantidade);
            
            if (mysqli_stmt_execute($stmt)) {
                $idLivro = mysqli_insert_id($connect);
                sendResponse(true, 'Livro cadastrado com sucesso', [
                    'id_livro' => $idLivro,
                    'titulo' => $titulo,
                    'autor' => $autor
                ]);
            } else {
                sendResponse(false, 'Erro ao cadastrar livro: ' . mysqli_error($connect));
            }
            
            mysqli_stmt_close($stmt);
            break;
            
        // LISTAR LIVROS
        case 'listar_livros':
            if ($method !== 'GET') {
                sendResponse(false, 'Método não permitido');
            }
            
            $sql = "SELECT idLivro, titulo, autor, sessao, editora, ano_publicacao, quantidade 
                    FROM livros 
                    WHERE quantidade > 0 
                    ORDER BY titulo ASC";
            $resultado = mysqli_query($connect, $sql);
            
            $livros = [];
            while ($livro = mysqli_fetch_assoc($resultado)) {
                $livros[] = $livro;
            }
            
            sendResponse(true, 'Livros listados com sucesso', $livros);
            break;
            
        // BUSCAR LIVROS
        case 'buscar_livros':
            if ($method !== 'GET') {
                sendResponse(false, 'Método não permitido');
            }
            
            $termo = $_GET['q'] ?? '';
            if (empty($termo)) {
                sendResponse(false, 'Termo de busca necessário');
            }
            
            $termo = "%$termo%";
            $sql = "SELECT idLivro, titulo, autor, sessao, editora, ano_publicacao, quantidade 
                    FROM livros 
                    WHERE (titulo LIKE ? OR autor LIKE ?) AND quantidade > 0 
                    ORDER BY titulo ASC";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $termo, $termo);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            
            $livros = [];
            while ($livro = mysqli_fetch_assoc($resultado)) {
                $livros[] = $livro;
            }
            
            mysqli_stmt_close($stmt);
            sendResponse(true, 'Busca realizada com sucesso', $livros);
            break;
            
        // INFO DO LIVRO
        case 'info_livro':
            if ($method !== 'GET') {
                sendResponse(false, 'Método não permitido');
            }
            
            $idLivro = intval($_GET['id'] ?? 0);
            if ($idLivro <= 0) {
                sendResponse(false, 'ID do livro inválido');
            }
            
            $sql = "SELECT idLivro, titulo, autor, sessao, editora, ano_publicacao, quantidade 
                    FROM livros 
                    WHERE idLivro = ?";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "i", $idLivro);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            
            if ($livro = mysqli_fetch_assoc($resultado)) {
                sendResponse(true, 'Livro encontrado', $livro);
            } else {
                sendResponse(false, 'Livro não encontrado');
            }
            
            mysqli_stmt_close($stmt);
            break;
            
        default:
            sendResponse(false, 'Ação não reconhecida. Use: login, cadastrar_livro, listar_livros, buscar_livros, info_livro');
    }
    
} catch (Exception $e) {
    sendResponse(false, 'Erro interno: ' . $e->getMessage());
}

mysqli_close($connect);
?>