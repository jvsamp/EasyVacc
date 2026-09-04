<?php
// backend/cors.php
require_once '../cors.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Trata requisições de verificação (Preflight OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
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