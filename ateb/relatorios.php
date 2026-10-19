<?php 
session_start();
if ($_SESSION['perfil'] !== 'admin') {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

// Zerar senhas
$mensagem = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zerar_senhas'])) {
    $pdo->exec("DELETE FROM senhas");
    $mensagem = "Todas as senhas foram zeradas com sucesso.";
}

// Obter todas as senhas com data de criação
$stmt = $pdo->query("SELECT tipo, codigo, data_criacao FROM senhas ORDER BY data_criacao DESC");
$senhas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatórios - Senhas Emitidas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; display: flex; overflow-x: hidden; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 60px; background-color: #343a40; overflow-x: hidden; transition: width 0.3s ease; display: flex; flex-direction: column; align-items: center; z-index: 1000; }
        .sidebar:hover { width: 220px; align-items: flex-start; }
        .sidebar img { width: 40px; margin: 10px; transition: all 0.3s ease; }
        .sidebar:hover img { width: 180px; margin: 10px 20px; }
        .sidebar a { width: 100%; color: #ccc; padding: 12px 20px; text-decoration: none; transition: all 0.3s ease; white-space: nowrap; display: flex; align-items: center; }
        .sidebar a:hover { background-color: #495057; color: #fff; transform: translateX(5px); }
        .sidebar a.bottom { margin-top: auto; background-color: #212529; }
        .main { margin-left: 60px; padding: 2rem; transition: margin-left 0.3s ease; flex: 1; }
        .sidebar:hover ~ .main { margin-left: 220px; }
        .container { max-width: 900px; margin: auto; }
        .card { border-radius: 1rem; box-shadow: 0 0 1rem rgba(0,0,0,0.1); background-color: #fff; }
        .table th, .table td { vertical-align: middle; }
        @media print { .no-print { display: none; } }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <img src="logo.png" alt="Logotipo" />
    <a href="painel.php"> <span class="ms-2">Painel</span></a>
    <a href="relatorios.php"> <span class="ms-2">Relatórios</span></a>
    <a href="configuracoes.php"> <span class="ms-2">Configurações</span></a>
    <a href="usuarios.php"> <span class="ms-2">Usuários</span></a>
    <a href="login.php" class="bottom text-white"> <span class="ms-2">Sair</span></a>
</div>

<!-- Conteúdo -->
<div class="main">
    <div class="container">
        <div class="card p-4">
            <h2 class="text-center mb-4">📋 Lista de Senhas Emitidas</h2>

            <?php if ($mensagem): ?>
                <div class="alert alert-success text-center"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Serviço</th>
                        <th>Senha</th>
                        <th>Data/Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($senhas as $senha): ?>
                        <tr>
                            <td><?= htmlspecialchars($senha['tipo']) ?></td>
                            <td><?= htmlspecialchars($senha['codigo']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($senha['data_criacao'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="text-center mt-3 no-print">
                <form method="post" onsubmit="return confirm('Tem certeza que deseja zerar todas as senhas? Esta ação não pode ser desfeita.');">
                    <button type="submit" name="zerar_senhas" class="btn btn-danger me-2">🗑 Zerar Senhas</button>
                    <button type="button" onclick="window.print()" class="btn btn-primary">🖨 Imprimir Relatório</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
