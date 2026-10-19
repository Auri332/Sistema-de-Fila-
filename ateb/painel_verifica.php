<?php
require 'conexao.php';
require_once 'servicos_kiosk.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$ultima = (string)($_GET['ultima'] ?? '');

$servicosConsulta = expandirServicosKiosk(array_keys(obterServicosKiosk()));

$chamada = null;
if ($servicosConsulta) {
    $placeholders = implode(',', array_fill(0, count($servicosConsulta), '?'));
    $stmt = $pdo->prepare("SELECT id, tipo, servico, codigo, balcao, chamado_em FROM senhas WHERE status = 'chamado' AND servico IN ($placeholders) ORDER BY chamado_em DESC, id DESC LIMIT 1");
    $stmt->execute($servicosConsulta);
    $chamada = $stmt->fetch(PDO::FETCH_ASSOC);
}
$evento = $chamada ? $chamada['id'] . '|' . $chamada['chamado_em'] : '';
$novaChamada = $chamada && $evento !== $ultima;
if ($chamada) {
    $chamada['evento'] = $evento;
}

$historico = [];
if ($servicosConsulta) {
    $placeholders = implode(',', array_fill(0, count($servicosConsulta), '?'));
    $stmt = $pdo->prepare("SELECT codigo, servico, DATE_FORMAT(chamado_em, '%H:%i') AS hora, balcao FROM senhas WHERE chamado_em IS NOT NULL AND servico IN ($placeholders) ORDER BY chamado_em DESC, id DESC LIMIT 8");
    $stmt->execute($servicosConsulta);
    $historico = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode([
    'novaChamada' => $novaChamada,
    'senha' => $novaChamada ? $chamada : null,
    'historico' => $historico,
], JSON_UNESCAPED_UNICODE);
