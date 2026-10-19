<?php
require 'conexao.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM senhas WHERE id = ?");
$stmt->execute([$id]);
$senha = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$senha) exit;

$mapa = [
    'geral' => 'Atendimento geral',
    'isenção' => 'Atendimento de isenção',
    'prioridade' => 'Atendimento de prioridade'
];

$frase = $mapa[$senha['servico']] . ", senha " . $senha['codigo'] . ", dirigir-se ao balcão " . $senha['balcao'];

// Usa serviço de voz do navegador (Web Speech API), ou sistema TTS no backend
header('Content-Type: audio/mpeg');

// Simulação de voz — use algo como Google Cloud TTS, Amazon Polly ou gTTS local
echo file_get_contents("https://api.voicerss.org/?key=SUA_CHAVE&hl=pt-br&src=" . urlencode($frase));
exit;
