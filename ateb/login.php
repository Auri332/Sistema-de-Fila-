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
        :root {
            --primary: #1d4ed8;
            --primary-dark: #0f172a;
            --bg1: #e0f2fe;
            --bg2: #f8fafc;
            --card: rgba(255,255,255,0.96);
            --muted: #475569;
            --border: rgba(148, 163, 184, 0.25);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--bg1) 0%, var(--bg2) 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        .login-wrapper {
            width: min(920px, 90vw);
            min-height: 540px;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.12);
        }

        .brand-panel {
            background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%);
            color: white;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-panel .badge {
            width: fit-content;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 999px;
            padding: 0.6rem 0.9rem;
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .brand-panel h1 {
            margin-top: 1.5rem;
            font-size: clamp(2.2rem, 4vw, 3.5rem);
            font-weight: 800;
            line-height: 1.1;
        }

        .brand-panel p {
            color: rgba(255,255,255,0.8);
            font-size: 1.05rem;
            line-height: 1.7;
            margin-bottom: 0;
        }

        .login-card {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 2rem;
        }

        .login-inner {
            width: 100%;
            max-width: 360px;
        }

        .login-inner h2 {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 0.7rem;
        }

        .login-inner .subtitle {
            color: var(--muted);
            margin-bottom: 1.6rem;
        }

        .form-control {
            height: 52px;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            padding-left: 1rem;
            background: #f8fafc;
        }

        .form-control:focus {
            border-color: rgba(29, 78, 216, 0.65);
            box-shadow: 0 0 0 0.25rem rgba(29, 78, 216, 0.12);
            background: #fff;
        }

        .btn-login {
            width: 100%;
            height: 52px;
            border-radius: 14px;
            font-weight: 700;
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            border: none;
        }

        .alert {
            border-radius: 14px;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .login-wrapper {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                padding: 2rem 1.5rem;
            }

            .login-card {
                padding: 2rem 1.25rem 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="brand-panel">
            <div class="badge">Sistema de fila</div>
            <h1>Atendimento rápido<br>e organizado.</h1>
            <p>Controle de senhas, atendimento por balcão, visualização em painel e gestão simples para operação em rede.</p>
        </div>

        <div class="login-card">
            <div class="login-inner">
                <h2>Login</h2>
                <div class="subtitle">Acesse o painel do sistema</div>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuário</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Digite seu usuário" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite sua senha" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-login">Entrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
