<?php    
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Redirecionamento por perfil
if ($_SESSION['perfil'] === 'admin') {
    header('Location: admin_dashboard.php');
    exit();
} elseif ($_SESSION['perfil'] === 'atendente') {
    header('Location: painel_atendimento.php');
    exit();
}

$nome = $_SESSION['nome'];
$perfil = $_SESSION['perfil'];
$balcao = $_SESSION['balcao'];
require 'carregar_configuracoes.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Painel do Cliente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: <?= htmlspecialchars($config['fonte']) ?>;
      font-size: <?= intval($config['tamanho']) ?>px;
      margin: 0;
      background-color: #f0f2f5;
      display: flex;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 60px;
      height: 100vh;
      background-color: #343a40;
      transition: width 0.3s ease;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .sidebar:hover {
      width: 220px;
    }

    .sidebar .logo {
      text-align: center;
      padding: 1rem;
    }

    .sidebar .logo img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }

    .sidebar nav {
      flex-grow: 1;
    }

    .sidebar a {
      color: #ccc;
      display: flex;
      align-items: center;
      padding: 12px 16px;
      text-decoration: none;
      transition: background 0.3s, color 0.3s;
    }

    .sidebar a:hover {
      background-color: #495057;
      color: #fff;
    }

    .sidebar a i {
      margin-right: 12px;
      min-width: 24px;
      text-align: center;
    }

    .logout {
      padding: 1rem;
    }

    .logout button {
      width: 100%;
    }

    .main-content {
      margin-left: 60px;
      padding: 2rem;
      width: 100%;
      transition: margin-left 0.3s ease;
    }

    .sidebar:hover ~ .main-content {
      margin-left: 220px;
    }

    .card-custom {
      border-radius: 1rem;
      box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
      background: white;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div>
      <div class="logo">
        <img src="logo.png" alt="Logo" title="Sistema" />
      </div>
      <nav>
        <?php if ($perfil === 'admin'): ?>
          <a href="admin_dashboard.php"><i></i> <span>Administração</span></a>
        <?php endif; ?>
      </nav>
    </div>
    <div class="logout">
      <form action="logout.php" method="post">
        <button class="btn btn-danger btn-sm">Sair</button>
      </form>
    </div>
  </div>

  <!-- Conteúdo principal -->
  <div class="main-content">
    <div class="card card-custom p-4">
      <h2 class="mb-3">Painel do Cliente</h2>
      <p><strong>Nome:</strong> <?= htmlspecialchars($nome) ?></p>
      <p><strong>Perfil:</strong> <span class="badge bg-secondary"><?= htmlspecialchars($perfil) ?></span></p>
      <p><strong>Balcão:</strong> <?= htmlspecialchars($balcao) ?></p>

      <h4 class="mt-4">Escolha um serviço para tirar a senha:</h4>
      <form action="gerar_senha.php" method="post" class="mt-3">
        <div class="d-grid gap-2">
          <button type="submit" name="servico" value="geral" class="btn btn-primary">Tirar Senha - Atendimento Geral</button>
          <button type="submit" name="servico" value="isenção" class="btn btn-info">Tirar Senha - Atendimento de Isenção</button>
          <button type="submit" name="servico" value="prioridade" class="btn btn-danger">Tirar Senha - Atendimento Prioritário</button>
        </div>
      </form>
    </div>
  </div>

</body>
</html>
