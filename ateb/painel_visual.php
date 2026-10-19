<?php
require 'conexao.php';
$stmt = $pdo->query("SELECT * FROM senhas WHERE visivel_painel = 1 ORDER BY chamado_em DESC LIMIT 1");
$senha = $stmt->fetch(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($senha ?: []);
