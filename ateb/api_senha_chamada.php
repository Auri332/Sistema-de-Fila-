<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'atendimento_novo';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT * FROM senhas WHERE status = 'chamado' ORDER BY chamado_em DESC, id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    $tipo = isset($row['tipo']) && $row['tipo'] !== '' ? $row['tipo'] : ($row['servico'] ?? '');

    echo json_encode([
        "id" => $row["id"],
        "chamado_em" => $row["chamado_em"],
        "tipo" => $tipo,
        "codigo" => $row["codigo"],
        "balcao" => $row["balcao"],
        "servico" => $row["servico"] ?? ''
    ]);
} else {
    echo json_encode([]);
}
$conn->close();
