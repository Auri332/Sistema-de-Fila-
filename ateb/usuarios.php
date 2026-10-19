<?php 
session_start();
if ($_SESSION['perfil'] !== 'admin') {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $perfil = $_POST['perfil'];
    $balcao = $_POST['balcao'];
    $servicosSelecionados = isset($_POST['servicos']) ? implode(', ', $_POST['servicos']) : '';

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, usuario, senha, perfil, balcao, servico) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nome, $usuario, $senha, $perfil, $balcao, $servicosSelecionados]);

    $mensagem = "Usuário cadastrado com sucesso!";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Gerenciar Usuários</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      background-color: #f0f2f5;
      font-family: Arial, sans-serif;
    }
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 60px;
      background-color: #343a40;
      overflow-x: hidden;
      transition: width 0.3s;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .sidebar:hover {
      width: 220px;
      align-items: flex-start;
    }
    .sidebar img {
      width: 40px;
      margin: 10px;
      transition: width 0.3s;
    }
    .sidebar:hover img {
      width: 180px;
      margin: 10px 20px;
    }
    .sidebar a {
      width: 100%;
      color: #ccc;
      padding: 12px 20px;
      text-decoration: none;
      transition: 0.3s;
      white-space: nowrap;
    }
    .sidebar a:hover {
      background-color: #495057;
      color: #fff;
    }
    .sidebar a.bottom {
      margin-top: auto;
      background-color: #212529;
      display: none;
    }
    .sidebar:hover a.bottom {
      display: block;
    }
    .main {
      margin-left: 60px;
      padding: 2rem;
      transition: margin-left 0.3s;
    }
    .sidebar:hover ~ .main {
      margin-left: 220px;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
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
    <a href="usuarios.php">Cadastrar Usuários</a>
    <a href="login.php" class="bottom text-center text-white">Logout </a>
  </div>

  <!-- Conteúdo principal -->
  <div class="main">
    <div class="container" style="max-width: 700px;">
      <div class="card p-4">
        <h2 class="mb-4 text-center">Cadastrar Novo Usuário</h2>
         
        <?php if ($mensagem): ?>
          <div class="alert alert-success text-center"><?= htmlspecialchars($mensagem) ?></div>
          <div class="d-grid gap-2 mt-3">
            <a href="admin_dashboard.php" class="btn btn-primary">⬅ Voltar</a>
          </div>
        <?php endif; ?>

        <form method="post" class="mt-4">
          <div class="mb-3">
            <label for="nome" class="form-label">Nome Completo</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
          </div>

          <div class="mb-3">
            <label for="usuario" class="form-label">Usuário (login)</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
          </div>

          <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" required>
          </div>

          <div class="mb-3">
            <label for="perfil" class="form-label">Perfil</label>
            <select class="form-select" id="perfil" name="perfil" required>
              <option value="atendente">Atendente</option>
              <option value="cliente">Cliente</option>
              <option value="admin">Administrador</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="balcao" class="form-label">Balcão</label>
            <input type="text" class="form-control" id="balcao" name="balcao">
          </div>

          <div class="mb-3">
            <label for="servicos" class="form-label">Serviços</label>
            <select class="form-select" id="servicos" name="servicos[]" multiple>
              <option value="atendimento geral">Atendimento Geral</option>
              <option value="isenção">Isenção</option>
              <option value="atendimento de prioridade">Atendimento de Prioridade</option>
            </select>
            <small class="text-muted">Use Ctrl (ou Cmd no Mac) para selecionar múltiplos</small>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-success">Cadastrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>
</html>
