<?php
require 'conexao.php';
$config = ['fonte' => 'Arial', 'tamanho' => '16'];

$stmt = $pdo->query("SELECT * FROM configuracoes LIMIT 1");
if ($stmt && $stmt->rowCount()) {
    $config = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
