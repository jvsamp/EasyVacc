<?php
// backend/conexao.php

$host = "db.mwgwuzxpoeahxswjgfee.supabase.co";
$port = "5432";
$dbName = "postgres";
$user = "postgres";
$password = "Qhk6Bqtvp3Whyi5C"; 
try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbName";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro na conexão com o Supabase: " . $e->getMessage()]);
    exit;
}
?>