<?php
// backend/api/utilidades.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../conexao.php';
require_once '../cors.php';
$tipo = $_GET['tipo'] ?? '';

try {
    if ($tipo === 'campanhas') {
        $stmt = $pdo->query("SELECT * FROM campanhas ORDER BY data_inicio ASC");
        echo json_encode($stmt->fetchAll());
    } elseif ($tipo === 'postos') {
        $stmt = $pdo->query("SELECT * FROM postos_saude ORDER BY nome ASC");
        echo json_encode($stmt->fetchAll());
    } else {
        http_response_code(400);
        echo json_encode(["erro" => "Parâmetro de tipo inválido."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro ao buscar dados: " . $e->getMessage()]);
}
?>