<?php  
session_start();
if ($_SESSION['perfil'] !== 'admin') {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

$stmt = $pdo->query("SELECT TRIM(servico) AS servico, COUNT(*) as total FROM senhas WHERE TRIM(servico) <> '' GROUP BY TRIM(servico) ORDER BY servico");
$estatisticas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $pdo->query("SELECT TRIM(servico) AS servico, COUNT(*) as usuarios FROM usuarios WHERE TRIM(servico) <> '' GROUP BY TRIM(servico) ORDER BY servico");
$usuariosServico = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>📊 Estatísticas do Sistema</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f2f5;
      display: flex;
      overflow-x: hidden;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 60px;
      background-color: #343a40;
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
      color: #ccc;
      padding: 12px 20px;
      text-decoration: none;
      transition: all 0.3s ease;
      white-space: nowrap;
      display: flex;
      align-items: center;
    }

    .sidebar a:hover {
      background-color: #495057;
      color: #fff;
      transform: translateX(5px);
    }

    .sidebar a.bottom {
      margin-top: auto;
      background-color: #212529;
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

    .painel {
      background: #fff;
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .btn-group {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 2rem;
    }

    #grafico, #tabelaGrafico {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease;
      display: none;
    }

    .mostrar {
      display: block !important;
      opacity: 1 !important;
      transform: translateY(0) !important;
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

<!-- Sidebar -->
<div class="sidebar">
  <img src="logo.png" alt="Logotipo" />
  <a href="admin_dashboard.php"> <span class="ms-2">Painel</span></a>
  <a href="estatisticas.php"> <span class="ms-2">Estatísticas</span></a>
  <a href="usuarios.php"> <span class="ms-2">Usuários</span></a>
  <a href="servicos.php"> <span class="ms-2">Serviços</span></a>
  <a href="configuracoes.php"> <span class="ms-2">Configurações</span></a>
  <a href="logout.php" class="bottom text-white"> <span class="ms-2">Logout</span></a>
</div>

<!-- Conteúdo -->
<div class="main">
  <div class="painel">
    <h2>📊 Estatísticas de Senhas por Serviço</h2>

    <div class="btn-group">
      <button onclick="window.print()" class="btn btn-outline-primary"> Imprimir</button>
      <form method="post" action="zerar_estatisticas.php" onsubmit="return confirm('Deseja realmente zerar os dados?')">
        <button type="submit" class="btn btn-outline-danger"> Zerar Tabela</button>
      </form>
      <button class="btn btn-outline-success" onclick="mostrarGrafico()"> Mostrar Gráfico</button>
      <button class="btn btn-outline-info" onclick="mostrarTabelaGrafico()"> Mostrar em Tabela</button>
      <button class="btn btn-outline-secondary" onclick="fecharVisualizacao()"> Fechar Visualização</button>
    </div>

    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Serviço</th>
          <th>Total de Senhas</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($estatisticas as $linha): ?>
          <tr>
            <td><?= htmlspecialchars($linha['servico']) ?></td>
            <td><?= $linha['total'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <h4 class="mt-5">👥 Usuários por Serviço</h4>
    <table class="table table-bordered table-hover">
      <thead class="table-secondary">
        <tr>
          <th>Serviço</th>
          <th>Quantidade de Usuários</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($usuariosServico as $linha): ?>
          <tr>
            <td><?= htmlspecialchars($linha['servico']) ?></td>
            <td><?= $linha['usuarios'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <canvas id="grafico"></canvas>

    <table class="table table-bordered mt-4" id="tabelaGrafico">
      <thead class="table-info">
        <tr>
          <th>Serviço</th>
          <th>Senhas</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($estatisticas as $linha): ?>
          <tr>
            <td><?= htmlspecialchars($linha['servico']) ?></td>
            <td><?= $linha['total'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
let graficoCriado = false;

function mostrarGrafico() {
  const canvas = document.getElementById('grafico');
  const tabela = document.getElementById('tabelaGrafico');

  canvas.classList.add('mostrar');
  tabela.classList.remove('mostrar');

  if (!graficoCriado) {
    const labels = <?= json_encode(array_column($estatisticas, 'servico')) ?>;
    const data = <?= json_encode(array_column($estatisticas, 'total')) ?>;

    new Chart(canvas, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Senhas por Serviço',
          data: data,
          backgroundColor: 'rgba(54, 162, 235, 0.6)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

    graficoCriado = true;
  }
}

function mostrarTabelaGrafico() {
  document.getElementById('grafico').classList.remove('mostrar');
  document.getElementById('tabelaGrafico').classList.add('mostrar');
}

function fecharVisualizacao() {
  document.getElementById('grafico').classList.remove('mostrar');
  document.getElementById('tabelaGrafico').classList.remove('mostrar');
}
</script>

</body>
</html>
