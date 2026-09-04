<?php
// backend/api/cadastro.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../conexao.php';

$data = json_decode(file_get_contents("php://input"), true);

// Valida campos obrigatórios baseados no formulário do frontend
if (!isset($data['nome']) || !isset($data['email']) || !isset($data['senha']) || !isset($data['cpf'])) {
    http_response_code(400);
    echo json_encode(["erro" => "Preencha todos os campos obrigatórios."]);
    exit;
}

$nome = $data['nome'];
$email = $data['email'];
$senhaHash = password_hash($data['senha'], PASSWORD_BCRYPT); // Criptografa a senha com segurança
$cpf = $data['cpf'];
$data_nascimento = $data['data_nascimento'] ?? null;
$telefone = $data['telefone'] ?? null;

try {
    // Verifica se o e-mail ou CPF já existem
    $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email OR cpf = :cpf");
    $stmtCheck->execute(['email' => $email, 'cpf' => $cpf]);
    
    if ($stmtCheck->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(["erro" => "E-mail ou CPF já cadastrados no sistema."]);
        exit;
    }

    // Insere o novo usuário no Supabase
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (nome, email, senha, cpf, data_nascimento, telefone, tipo) 
        VALUES (:nome, :email, :senha, :cpf, :data_nascimento, :telefone, 'cidadao')
    ");
    
    $stmt->execute([
        'nome' => $nome,
        'email' => $email,
        'senha' => $senhaHash,
        'cpf' => $cpf,
        'data_nascimento' => $data_nascimento,
        'telefone' => $telefone
    ]);

    http_response_code(201);
    echo json_encode(["sucesso" => true, "mensagem" => "Cadastro realizado com sucesso!"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro ao cadastrar usuário: " . $e->getMessage()]);
}
?>