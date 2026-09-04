<?php
// backend/api/vacinas.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../conexao.php';
require_once '../cors.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $usuario_id = $_GET['usuario_id'] ?? null;

    try {
        // 1. Busca a lista geral de vacinas cadastradas no sistema
        $stmtVacinas = $pdo->query("SELECT * FROM vacinas ORDER BY nome ASC");
        $vacinas = $stmtVacinas->fetchAll();

        $historico = [];
        
        // 2. Se o ID do usuário foi passado, busca também o histórico de vacinação dele
        if ($usuario_id) {
            $stmtHist = $pdo->prepare("
                SELECT r.*, v.nome as vacina_nome, v.descricao as vacina_descricao 
                FROM registros_vacinacao r
                JOIN vacinas v ON r.vacina_id = v.id
                WHERE r.usuario_id = :usuario_id
                ORDER BY r.data_aplicacao DESC
            ");
            $stmtHist->execute(['usuario_id' => $usuario_id]);
            $historico = $stmtHist->fetchAll();
        }

        echo json_encode([
            "sucesso" => true,
            "vacinas_disponiveis" => $vacinas,
            "historico_usuario" => $historico
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro ao buscar dados da dashboard: " . $e->getMessage()]);
    }
}
?>