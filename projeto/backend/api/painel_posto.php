<?php
// backend/api/painel_posto.php
require_once '../cors.php';
require_once '../conexao.php';

$method = $_SERVER['REQUEST_METHOD'];

// Buscar cidadão por CPF / SUS
if ($method === 'GET') {
    $busca = $_GET['busca'] ?? null;

    if (!$busca) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe o CPF ou Cartão SUS para busca."]);
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT id, nome, cpf, data_nascimento, telefone FROM usuarios WHERE cpf = :busca");
        $stmt->execute(['busca' => $busca]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            echo json_encode(["sucesso" => true, "usuario" => $usuario]);
        } else {
            http_response_code(404);
            echo json_encode(["erro" => "Cidadão não encontrado."]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro na busca: " . $e->getMessage()]);
    }
    exit();
}

// Registrar aplicação de vacina
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $usuario_id = $data['usuario_id'] ?? null;
    $vacina_id = $data['vacina_id'] ?? null;
    $lote = $data['lote'] ?? null;
    $local = $data['local_aplicacao'] ?? 'Posto de Saúde';

    if (!$usuario_id || !$vacina_id || !$lote) {
        http_response_code(400);
        echo json_encode(["erro" => "Campos obrigatórios ausentes (usuario_id, vacina_id, lote)."]);
        exit();
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO vacinas_aplicadas (usuario_id, vacina_id, lote, local_aplicacao, data_aplicacao)
            VALUES (:usuario_id, :vacina_id, :lote, :local, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            'usuario_id' => $usuario_id,
            'vacina_id'  => $vacina_id,
            'lote'        => $lote,
            'local'       => $local
        ]);

        http_response_code(201);
        echo json_encode(["sucesso" => true, "mensagem" => "Vacina registrada no sistema!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro ao registrar vacina: " . $e->getMessage()]);
    }
    exit();
}
?>