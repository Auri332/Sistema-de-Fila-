<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

if (!isset($_SESSION['usuario_id']) || ($_SESSION['perfil'] ?? '') !== 'atendente') {
    http_response_code(401);
    echo json_encode(['erro' => 'Acesso não autorizado.']);
    exit;
}

require 'conexao.php';
require_once 'servicos_kiosk.php';

$servico = trim((string)($_GET['servico'] ?? ''));
$servicosDisponiveis = obterServicosKiosk();
if (!isset($servicosDisponiveis[$servico])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Selecione um serviço válido.']);
    exit;
}

$servicosConsulta = expandirServicosKiosk([$servico]);
$placeholders = implode(',', array_fill(0, count($servicosConsulta), '?'));
$stmt = $pdo->prepare("SELECT codigo, servico FROM senhas WHERE servico IN ($placeholders) AND status = 'aguardando' ORDER BY id ASC LIMIT 1");
$stmt->execute($servicosConsulta);
$senha = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode(['senha' => $senha ?: null], JSON_UNESCAPED_UNICODE);