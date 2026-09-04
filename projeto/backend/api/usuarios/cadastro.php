<?php
// backend/api/cadastro.php

// 1. Configuração dos cabeçalhos CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// 2. Resposta imediata para a requisição Preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once '../cors.php';
require_once '../conexao.php';

// Captura e decodifica o JSON do corpo da requisição
$data = json_decode(file_get_contents("php://input"), true);

// Extração dos campos enviados
$nome = $data['nome'] ?? null;
$email = $data['email'] ?? null;
$senha = $data['senha'] ?? null;
$cpf = $data['cpf'] ?? $data['cartao_sus'] ?? null; // Suporta CPF ou Cartão SUS caso venha com esse nome
$data_nascimento = $data['data_nascimento'] ?? null;
$telefone = $data['telefone'] ?? null;

// Validação dos campos obrigatórios
if (!$nome || !$email || !$senha) {
    http_response_code(400);
    echo json_encode(["erro" => "Preencha os campos obrigatórios (nome, e-mail e senha)."]);
    exit();
}

$senhaHash = password_hash($senha, PASSWORD_BCRYPT);

try {
    // Verifica duplicidade por e-mail ou CPF (se o CPF tiver sido informado)
    if ($cpf) {
        $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email OR cpf = :cpf");
        $stmtCheck->execute(['email' => $email, 'cpf' => $cpf]);
    } else {
        $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmtCheck->execute(['email' => $email]);
    }
    
    if ($stmtCheck->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(["erro" => "E-mail ou CPF já cadastrados no sistema."]);
        exit();
    }

    // Inserção no banco de dados
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (nome, email, senha, cpf, data_nascimento, telefone, tipo) 
        VALUES (:nome, :email, :senha, :cpf, :data_nascimento, :telefone, 'cidadao')
    ");
    
    $stmt->execute([
        'nome'            => $nome,
        'email'           => $email,
        'senha'           => $senhaHash,
        'cpf'             => $cpf,
        'data_nascimento' => $data_nascimento,
        'telefone'        => $telefone
    ]);

    http_response_code(201);
    echo json_encode(["sucesso" => true, "mensagem" => "Cadastro realizado com sucesso!"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro no banco de dados: " . $e->getMessage()]);
}
?>