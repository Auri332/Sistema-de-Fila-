<?php
require 'conexao.php';
$stmt = $pdo->prepare("SELECT * FROM senhas WHERE status = 'chamado' ORDER BY chamado_em DESC LIMIT 1");
$stmt->execute();
$senha = $stmt->fetch(PDO::FETCH_ASSOC);

if ($senha) {
    echo json_encode([
        'codigo' => $senha['codigo'],
        'tipo' => ucfirst($senha['tipo']),
        'balcao' => $senha['balcao'],
        'status' => $senha['status']
    ]);
} else {
    echo json_encode(null);
}
?>
