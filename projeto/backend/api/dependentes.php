<?php
// backend/api/dependentes.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../conexao.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $usuario_id = $_GET['usuario_id'] ?? null;
    if (!$usuario_id) {
        http_response_code(400);
        echo json_encode(["erro" => "ID do usuário obrigatório"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM dependentes WHERE usuario_id = :usuario_id ORDER BY nome ASC");
        $stmt->execute(['usuario_id' => $usuario_id]);
        echo json_encode($stmt->fetchAll());
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro ao buscar dependentes: " . $e->getMessage()]);
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['usuario_id']) || !isset($data['nome']) || !isset($data['data_nascimento']) || !isset($data['parentesco'])) {
        http_response_code(400);
        echo json_encode(["erro" => "Preencha todos os campos do dependente."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO dependentes (usuario_id, nome, data_nascimento, parentesco) 
            VALUES (:usuario_id, :nome, :data_nascimento, :parentesco)
        ");
        $stmt->execute([
            'usuario_id' => $data['usuario_id'],
            'nome' => $data['nome'],
            'data_nascimento' => $data['data_nascimento'],
            'parentesco' => $data['parentesco']
        ]);

        http_response_code(201);
        echo json_encode(["sucesso" => true, "mensagem" => "Dependente cadastrado com sucesso!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro ao cadastrar dependente: " . $e->getMessage()]);
    }
}
?>