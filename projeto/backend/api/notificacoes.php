<?php
// backend/api/notificacoes.php
require_once '../cors.php';
require_once '../conexao.php';

$usuarioId = $_GET['usuario_id'] ?? $_GET['usuarioId'] ?? null;

try {
    if ($usuarioId) {
        $stmt = $pdo->prepare("
            SELECT id, titulo, mensagem, data_envio, lida 
            FROM notificacoes 
            WHERE usuario_id = :id OR usuario_id IS NULL 
            ORDER BY data_envio DESC
        ");
        $stmt->execute(['id' => $usuarioId]);
    } else {
        $stmt = $pdo->query("SELECT id, titulo, mensagem, data_envio FROM notificacoes WHERE usuario_id IS NULL ORDER BY data_envio DESC");
    }

    $notificacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "sucesso" => true,
        "notificacoes" => $notificacoes
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro ao buscar notificações: " . $e->getMessage()]);
}
?>