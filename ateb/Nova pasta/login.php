<?php  
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dados && $senha === $dados['senha']) {
        $_SESSION['usuario_id'] = $dados['id'];
        $_SESSION['nome'] = $dados['nome'] ?? 'Usuário';
        $_SESSION['perfil'] = $dados['perfil'] ?? 'atendente';
        $_SESSION['balcao'] = $dados['balcao'] ?? '';
        $_SESSION['servico'] = $dados['servico'] ?? '';

        // Redireciona com base no perfil
        if ($_SESSION['perfil'] === 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: painel_atendimento.php');
        }
        exit();
    } else {
        $erro = 'Usuário ou senha inválidos.';
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Atendimento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgb(152, 161, 167);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 1rem rgba(0,0,0,0.1);
            background-color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 class="text-center mb-4">🔐 Login</h2>
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuário</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Entrar</button>
            </div>
        </form>
    </div>
</body>
</html>
