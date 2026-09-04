<?php
// backend/api/login.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../conexao.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['senha'])) {
    http_response_code(400);
    echo json_encode(["erro" => "E-mail e senha são obrigatórios"]);
    exit;
}

$email = $data['email'];
$senha = $data['senha'];

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Remove a senha antes de retornar os dados do usuário
        unset($usuario['senha']);
        echo json_encode(["sucesso" => true, "usuario" => $usuario]);
    } else {
        http_response_code(401);
        echo json_encode(["erro" => "Credenciais inválidas"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro no servidor: " . $e->getMessage()]);
}
?>