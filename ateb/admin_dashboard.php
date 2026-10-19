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

$hoje = date('Y-m-d');

$totalSenhasHoje = (int)$pdo->query("SELECT COUNT(*) FROM senhas WHERE DATE(data_criacao) = CURDATE()")->fetchColumn();
$totalAguardando = (int)$pdo->query("SELECT COUNT(*) FROM senhas WHERE status = 'aguardando'")->fetchColumn();
$totalChamados = (int)$pdo->query("SELECT COUNT(*) FROM senhas WHERE status = 'chamado'")->fetchColumn();
$totalAtendentes = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE perfil = 'atendente'")->fetchColumn();
$ultimasSenhas = $pdo->query("SELECT codigo, servico, status, data_criacao FROM senhas ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Painel do Administrador</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #eef6ff 0%, #f7fbff 100%);
      display: flex;
      overflow-x: hidden;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 60px;
      background: rgba(26, 35, 51, 0.98);
      overflow-x: hidden;
      transition: width 0.3s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      z-index: 1000;
    }

    .sidebar:hover {
      width: 220px;
      align-items: flex-start;
    }

    .sidebar img {
      width: 40px;
      margin: 10px;
      transition: all 0.3s ease;
    }

    .sidebar:hover img {
      width: 180px;
      margin: 10px 20px;
    }

    .sidebar a {
      width: 100%;
      color: #dfe9f5;
      padding: 12px 20px;
      text-decoration: none;
      transition: all 0.3s ease;
      white-space: nowrap;
      display: flex;
      align-items: center;
    }

    .sidebar a:hover {
      background: rgba(255,255,255,0.08);
      color: #fff;
      transform: translateX(5px);
    }

    .sidebar a.bottom {
      margin-top: auto;
      background-color: #111827;
    }

    .main {
      margin-left: 60px;
      padding: 2rem;
      transition: margin-left 0.3s ease;
      flex: 1;
    }

    .sidebar:hover ~ .main {
      margin-left: 220px;
    }

    .card {
      border: 0;
      border-radius: 1.2rem;
      box-shadow: 0 15px 35px rgba(15, 76, 129, 0.10);
      background: #fff;
    }

    .header {
      font-size: clamp(1.8rem, 2vw, 2.5rem);
      font-weight: 800;
      color: #0f172a;
    }

    .stat-card {
      border-radius: 1rem;
      padding: 1.25rem;
      color: #fff;
      min-height: 130px;
    }

    .bg-blue { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
    .bg-green { background: linear-gradient(135deg, #10b981, #059669); }
    .bg-orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .bg-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    .quick-link {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 120px;
      font-weight: 700;
      border-radius: 1rem;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      text-decoration: none;
      color: #1f2937;
      transition: all 0.2s ease;
    }

    .quick-link:hover {
      transform: translateY(-2px);
      background: #eff6ff;
      text-decoration: none;
      color: #0f172a;
    }

    @media (max-width: 768px) {
      .sidebar {
        display: none;
      }
      .main {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <img src="logo.png" alt="Logotipo" />
    <a href="painel.php"> <span class="ms-2">Painel</span></a>
    <a href="relatorios.php"> <span class="ms-2">Relatórios</span></a>
    <a href="configuracoes.php"> <span class="ms-2">Configurações</span></a>
    <a href="configuracoes.php#licenca"> <span class="ms-2">Licença</span></a>
    <a href="usuarios.php"> <span class="ms-2">Usuários</span></a>
    <a href="login.php" class="bottom text-white"> <span class="ms-2">Logout</span></a>
  </div>

  <div class="main">
    <div class="container-fluid">
      <div class="card p-4 mb-4">
        <div class="header mb-2">🔧 Painel do Administrador</div>
        <p class="text-muted mb-0">Resumo operacional do dia e acesso rápido às funções do sistema.</p>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <div class="stat-card bg-blue">
            <small>Total hoje</small>
            <h2 class="mt-2 mb-0"><?= $totalSenhasHoje ?></h2>
            <span>senhas emitidas</span>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card bg-green">
            <small>Em espera</small>
            <h2 class="mt-2 mb-0"><?= $totalAguardando ?></h2>
            <span>aguardando atendimento</span>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card bg-orange">
            <small>Chamadas</small>
            <h2 class="mt-2 mb-0"><?= $totalChamados ?></h2>
            <span>senhas em atendimento</span>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card bg-purple">
            <small>Atendentes</small>
            <h2 class="mt-2 mb-0"><?= $totalAtendentes ?></h2>
            <span>usuários ativos</span>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <a class="quick-link" href="painel.php">📊 Estatísticas</a>
        </div>
        <div class="col-md-3">
          <a class="quick-link" href="relatorios.php">🧾 Relatórios</a>
        </div>
        <div class="col-md-3">
          <a class="quick-link" href="usuarios.php">👥 Usuários</a>
        </div>
        <div class="col-md-3">
          <a class="quick-link" href="configuracoes.php">⚙️ Configurações</a>
        </div>
      </div>

      <div class="card p-4 mb-4" id="licenca">
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

      <div class="card p-4">
        <h4 class="mb-3">🕒 Últimas senhas registradas</h4>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Código</th>
                <th>Serviço</th>
                <th>Status</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($ultimasSenhas as $senha): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($senha['codigo']) ?></strong></td>
                  <td><?= htmlspecialchars($senha['servico']) ?></td>
                  <td>
                    <?php
                      $status = htmlspecialchars($senha['status']);
                      $badge = 'secondary';
                      if ($status === 'aguardando') $badge = 'warning';
                      if ($status === 'chamado') $badge = 'success';
                      if ($status === 'nao_apareceu') $badge = 'danger';
                    ?>
                    <span class="badge bg-<?= $badge ?>"><?= $status ?></span>
                  </td>
                  <td><?= date('d/m/Y H:i', strtotime($senha['data_criacao'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
