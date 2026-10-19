<?php
session_start();
if ($_SESSION['perfil'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Painel do Administrador</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary: #0f4c81;
      --primary-strong: #072f52;
      --primary-soft: #dfeeff;
      --accent: #f7b731;
      --bg: linear-gradient(135deg, #edf5ff 0%, #dfeaf7 30%, #eef7ff 100%);
      --panel: rgba(255,255,255,0.9);
      --text: #1a2433;
      --muted: #5c6b7a;
      --sidebar: rgba(9, 37, 59, 0.98);
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: 'Segoe UI', Arial, sans-serif;
      background: var(--bg);
      color: var(--text);
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 78px;
      background: var(--sidebar);
      overflow-x: hidden;
      transition: width 0.35s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      box-shadow: 8px 0 24px rgba(9, 37, 59, 0.18);
      z-index: 20;
    }

    .sidebar:hover {
      width: 240px;
      align-items: flex-start;
    }

    .sidebar img {
      width: 48px;
      margin: 16px 12px 10px;
      transition: all 0.35s ease;
      border-radius: 14px;
      filter: drop-shadow(0 8px 18px rgba(0,0,0,0.15));
    }

    .sidebar:hover img {
      width: 170px;
      margin: 16px 20px 14px;
    }

    .sidebar a {
      width: 100%;
      color: #d7e7fb;
      padding: 14px 20px;
      text-decoration: none;
      transition: 0.25s ease;
      white-space: nowrap;
      font-weight: 600;
      letter-spacing: 0.02em;
    }

    .sidebar a:hover {
      background: linear-gradient(90deg, rgba(247,183,49,0.22), rgba(15,76,129,0.2));
      color: #fff;
      transform: translateX(4px);
    }

    .sidebar a.bottom {
      margin-top: auto;
      background: rgba(255,255,255,0.06);
      border-top: 1px solid rgba(255,255,255,0.08);
    }

    .main {
      margin-left: 78px;
      padding: 2.2rem;
      transition: margin-left 0.35s ease;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .sidebar:hover ~ .main {
      margin-left: 240px;
    }

    .card {
      border: 0;
      border-radius: 26px;
      box-shadow: 0 24px 50px rgba(15, 76, 129, 0.12);
      background: var(--panel);
      backdrop-filter: blur(10px);
      max-width: 920px;
      width: min(100%, 920px);
      padding: 2rem;
    }

    .header {
      font-size: clamp(1.8rem, 2vw, 2.4rem);
      font-weight: 800;
      color: var(--primary-strong);
      letter-spacing: -0.03em;
    }

    .welcome-copy {
      color: var(--muted);
      font-size: 1.08rem;
      line-height: 1.7;
    }

    @media (max-width: 768px) {
      .main {
        margin-left: 0;
        padding: 1rem;
      }

      .sidebar {
        width: 100%;
        height: auto;
        position: sticky;
        top: 0;
        flex-direction: row;
        align-items: center;
        overflow: visible;
      }

      .sidebar:hover {
        width: 100%;
      }

      .sidebar img {
        width: 42px;
      }

      .sidebar nav {
        display: flex;
        overflow-x: auto;
      }

      .sidebar a {
        min-width: max-content;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="logo.png" alt="Logotipo" />
    <a href="admin_dashboard.php"> Painel</a>
    <a href="relatorios.php"> Relatórios</a>
    <a href="configuracoes.php"> Configurações</a>
    <a href="usuarios.php"> Usuários</a>
    <a href="login.php" class="bottom text-center text-white">Voltar</a>
  </div>

  <!-- Conteúdo -->
  <div class="main">
    <div class="container">
      <div class="card p-4">
        <div class="header mb-3"> Painel do Administrador</div>
        <p>Bem-vindo ao painel administrativo. Use o menu à esquerda para acessar as funcionalidades disponíveis.</p>
      </div>
    </div>
  </div>

</body>
</html>
