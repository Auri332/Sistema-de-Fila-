<?php    
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require 'carregar_configuracoes.php';
require_once 'conexao.php';

$nome = $_SESSION['nome'];
$perfil = $_SESSION['perfil'];
$balcao = $_SESSION['balcao'];
$usuario_id = $_SESSION['usuario_id'];

$dadosUsuario = [
    'nome' => $nome,
    'balcao' => $balcao,
    'servico' => ''
];

// Obter dados adicionais se for atendente
if ($perfil === 'atendente') {
    $stmt = $pdo->prepare("SELECT servico FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($dados) {
        $dadosUsuario['servico'] = $dados['servico'];
    }

    // Chamar senha
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chamar'])) {
        $stmt = $pdo->prepare("SELECT * FROM senhas WHERE servico = ? AND status = 'aguardando' ORDER BY id ASC LIMIT 1");
        $stmt->execute([$dadosUsuario['servico']]);
        $proximaSenha = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($proximaSenha) {
            $stmt = $pdo->prepare("UPDATE senhas SET status = 'chamado', atendente = ?, balcao = ?, chamado_em = NOW() WHERE id = ?");
            $stmt->execute([$dadosUsuario['nome'], $dadosUsuario['balcao'], $proximaSenha['id']]);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Painel de Atendimento</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary: #0f4c81;
      --primary-strong: #072f52;
      --secondary: #f7b731;
      --bg: linear-gradient(135deg, #eff6ff 0%, #dfeaf7 28%, #eef8ff 100%);
      --panel: rgba(255,255,255,0.94);
      --text: #1b2430;
      --muted: #53657a;
      --sidebar: rgba(10, 33, 52, 0.98);
    }

    * { box-sizing: border-box; }

    body {
      font-family: <?= htmlspecialchars($config['fonte']) ?>;
      font-size: <?= intval($config['tamanho']) ?>px;
      margin: 0;
      background: var(--bg);
      color: var(--text);
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 84px;
      height: 100vh;
      background: var(--sidebar);
      transition: width 0.35s ease;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: 10px 0 28px rgba(11, 30, 49, 0.12);
      z-index: 20;
    }

    .sidebar:hover { width: 240px; }
    .sidebar .logo { text-align: center; padding: 1rem 0.75rem; }
    .sidebar .logo img {
      width: 58px;
      height: 52px;
      border-radius: 16px;
      object-fit: cover;
      box-shadow: 0 10px 20px rgba(0,0,0,0.14);
    }
    .sidebar nav { flex-grow: 1; }
    .sidebar a {
      color: #dbeafc;
      display: flex;
      align-items: center;
      padding: 14px 18px;
      text-decoration: none;
      transition: background 0.25s ease, transform 0.25s ease, color 0.25s ease;
      font-weight: 600;
    }
    .sidebar a:hover {
      background: linear-gradient(90deg, rgba(247,183,49,0.22), rgba(15,76,129,0.18));
      color: #fff;
      transform: translateX(3px);
    }
    .sidebar a i { margin-right: 22px; min-width: 30px; text-align: center; }
    .logout { padding: 1rem; }
    .logout button {
      width: 100%;
      border-radius: 12px;
      font-weight: 700;
    }
    .main-content {
      margin-left: 84px;
      padding: 2.2rem;
      width: 100%;
      transition: margin-left 0.35s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .sidebar:hover ~ .main-content { margin-left: 240px; }
    .card-custom {
      border: 0;
      border-radius: 28px;
      box-shadow: 0 25px 60px rgba(23, 54, 84, 0.14);
      background: var(--panel);
      max-width: 980px;
      width: min(100%, 980px);
      padding: 2rem;
    }
    .senha-display {
      font-size: clamp(3rem, 7vw, 7rem);
      font-weight: 900;
      color: var(--primary);
      text-align: center;
      margin: 1.5rem 0;
      letter-spacing: 0.06em;
      text-shadow: 0 8px 24px rgba(15, 76, 129, 0.12);
    }

    .badge {
      font-size: 0.9rem;
      padding: 0.6rem 0.9rem;
      border-radius: 999px;
    }

    .alert-info {
      border-radius: 16px;
      font-weight: 600;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:horizontal {
        background: linear-gradient(to right, #00BFFF, #1E3A8A);
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(240, 248, 255, 0.8);
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:vertical {
        background: linear-gradient(to bottom, #00BFFF, #1E3A8A);
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    @media (max-width: 768px) {
      body { display: block; }
      .sidebar {
        position: sticky;
        top: 0;
        width: 100%;
        height: auto;
        flex-direction: row;
        align-items: center;
        overflow: visible;
      }
      .sidebar:hover { width: 100%; }
      .sidebar nav {
        display: flex;
        overflow-x: auto;
      }
      .main-content {
        margin-left: 0;
        padding: 1rem;
      }
      .card-custom { padding: 1.25rem; }
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
        <a href="atendimentoo.php"><i></i> <span>Atendimento</span></a>
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
    <div class="card card-custom p-4 text-center">
      <h2 class="mb-3">Seja Bem-Vindo</h2>
      <p><strong>Nome:</strong> <?= htmlspecialchars($nome) ?></p>
      <p><strong>Perfil:</strong> <span class="badge bg-secondary"><?= htmlspecialchars($perfil) ?></span></p>
      <p><strong>Balcão:</strong> <?= htmlspecialchars($balcao) ?></p>

      <?php if ($perfil === 'atendente'): ?>
        <hr>
        <h4>Serviço:  <?= htmlspecialchars($dadosUsuario['servico']) ?></h4>
        <?php if (!empty($proximaSenha)): ?>
          <div class="senha-display"><?= htmlspecialchars($proximaSenha['codigo']) ?></div>
          <div class="alert alert-info">Senha chamada com sucesso!</div>
        <?php else: ?>
          
        <?php endif; ?>
         <?php if ($perfil === 'cliente'): ?>
        <a href="atendimentoo.php" class="btn btn-primary btn-atendimento">Acessar aos Serviços </a>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>
