<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

if (!isset($_SESSION['usuario_id']) || ($_SESSION['perfil'] ?? '') !== 'atendente') {
    http_response_code(401);
    echo json_encode(['senhas' => [], 'estatisticas' => ['aguardando' => 0, 'chamados' => 0, 'totalHoje' => 0]]);
    exit;
}

require 'conexao.php';
require_once 'servicos_kiosk.php';

$servicosConsulta = expandirServicosKiosk(array_keys(obterServicosKiosk()));

$senhas = [];
$estatisticas = ['aguardando' => 0, 'chamados' => 0, 'totalHoje' => 0];
if ($servicosConsulta) {
    $placeholders = implode(',', array_fill(0, count($servicosConsulta), '?'));
    $stmt = $pdo->prepare("SELECT codigo, servico, status, balcao, atendente FROM senhas WHERE status = 'aguardando' AND servico IN ($placeholders) ORDER BY id ASC LIMIT 10");
    $stmt->execute($servicosConsulta);
    $senhas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT SUM(status = 'aguardando') AS aguardando, SUM(status = 'chamado') AS chamados, SUM(DATE(data_criacao) = CURDATE()) AS totalHoje FROM senhas WHERE servico IN ($placeholders)");
    $stmt->execute($servicosConsulta);
    $estatisticas = array_map('intval', $stmt->fetch(PDO::FETCH_ASSOC) ?: $estatisticas);
}

echo json_encode(['senhas' => $senhas, 'estatisticas' => $estatisticas], JSON_UNESCAPED_UNICODE);