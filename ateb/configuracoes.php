<?php
session_start();
if ($_SESSION['perfil'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require 'conexao.php';

$mensagemLicenca = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['licenca_codigo'])) {
  $resultado = ativarLicenca($pdo, $_POST['licenca_codigo']);
  $mensagemLicenca = $resultado['mensagem'];
}

$licencaAtual = buscarLicencaValida($pdo);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Configurações do Sistema</title>
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
    <a href="usuarios.php"> Usuários</a>
    <a href="relatorios.php"> Relatórios</a>
    <a href="configuracoes.php" class="active"> Configurações</a>
    <a href="configuracoes.php#licenca"> Licença</a>
    <a href="admin_dashboard.php" class="bottom text-center text-white">⬅ Voltar</a>
  </div>

  <!-- Conteúdo -->
  <div class="main">
    <div class="container">
      <div class="card p-4">
        <h2 class="text-center mb-4">⚙️ Personalização do Sistema</h2>

        <div class="card border-0 bg-light p-4 mb-4" id="licenca">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h4 class="mb-0">🔑 Licença anual</h4>
            <?php if ($licencaAtual): ?>
              <span class="badge bg-success">Ativa até <?= date('d/m/Y', strtotime($licencaAtual['data_validade'])) ?></span>
            <?php else: ?>
              <span class="badge bg-danger">Sem licença ativa</span>
            <?php endif; ?>
          </div>

          <?php if ($mensagemLicenca): ?>
            <div class="alert <?= str_contains($mensagemLicenca, 'sucesso') ? 'alert-success' : 'alert-warning' ?> mb-3">
              <?= htmlspecialchars($mensagemLicenca) ?>
            </div>
          <?php endif; ?>

          <form method="post" class="row g-2 align-items-end">
            <div class="col-md-9">
              <label for="licenca_codigo" class="form-label">Código da licença</label>
              <input type="text" id="licenca_codigo" name="licenca_codigo" class="form-control" placeholder="EX: KIOK-2026-ABC123" required>
            </div>
            <div class="col-md-3 d-grid">
              <button type="submit" class="btn btn-primary">Ativar licença</button>
            </div>
          </form>
        </div>

        <form action="#" method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="fonte" class="form-label">Fonte do sistema:</label>
            <input type="text" class="form-control" id="fonte" name="fonte" value="Arial">
          </div>

          <div class="mb-3">
            <label for="tamanho" class="form-label">Tamanho da fonte (px):</label>
            <input type="number" class="form-control" id="tamanho" name="tamanho" value="16">
          </div>

          <div class="mb-3">
            <label for="logo" class="form-label">Logotipo (imagem):</label>
            <input type="file" class="form-control" id="logo" name="logo">
          </div>

          <div class="mb-3">
            <label for="audio" class="form-label">Importar áudio por serviço:</label>
            <input type="file" class="form-control" id="audio" name="audio">
          </div>

          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success"> Salvar configurações</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>
</html>
