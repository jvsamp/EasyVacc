<?php
// backend/api/certificado.php
require_once '../cors.php';
require_once '../conexao.php';

$usuarioId = $_GET['usuario_id'] ?? $_GET['usuarioId'] ?? null;

if (!$usuarioId) {
    http_response_code(400);
    echo json_encode(["erro" => "ID do usuário é obrigatório."]);
    exit();
}

try {
    // Busca dados básicos do cidadão
    $stmtUser = $pdo->prepare("SELECT id, nome, cpf, data_nascimento FROM usuarios WHERE id = :id");
    $stmtUser->execute(['id' => $usuarioId]);
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        http_response_code(404);
        echo json_encode(["erro" => "Usuário não encontrado."]);
        exit();
    }

    // Busca histórico de doses aplicadas
    $stmtVacinas = $pdo->prepare("
        SELECT v.nome AS vacina, va.data_aplicacao, va.lote, va.local_aplicacao
        FROM vacinas_aplicadas va
        JOIN vacinas v ON va.vacina_id = v.id
        WHERE va.usuario_id = :id
        ORDER BY va.data_aplicacao DESC
    ");
    $stmtVacinas->execute(['id' => $usuarioId]);
    $vacinas = $stmtVacinas->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "sucesso" => true,
        "usuario" => $usuario,
        "vacinas" => $vacinas
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro ao gerar certificado: " . $e->getMessage()]);
}
?>