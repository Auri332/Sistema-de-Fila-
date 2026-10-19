<?php
session_start();
require 'conexao.php';
require_once 'servicos_kiosk.php';

$mensagemLicenca = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['licenca_codigo'])) {
    $resultado = ativarLicenca($pdo, $_POST['licenca_codigo']);
    $mensagemLicenca = $resultado['mensagem'];
}

$licencaAtual = buscarLicencaValida($pdo);
$licencaValida = !empty($licencaAtual);

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['perfil'], ['atendente', 'cliente'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$perfil = $_SESSION['perfil'];
$servicosKiosk = obterServicosKiosk();

$stmt = $pdo->prepare("SELECT nome, servico, balcao FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);

function gerarCodigo($tipo) {
    global $pdo, $servicosKiosk;
    $letra = $servicosKiosk[$tipo] ?? 'X';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM senhas WHERE servico = ? AND DATE(data_criacao) = CURDATE()");
    $stmt->execute([$tipo]);
    $total = $stmt->fetchColumn();
    return $letra . str_pad($total + 1, 3, '0', STR_PAD_LEFT);
}

// === CLIENTE ===
if ($perfil === 'cliente') {
    $ultimasSenhasCliente = [];
    $urlImpressao = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tipo = $_POST['tipo'] ?? '';
        if (isset($servicosKiosk[$tipo])) {
            $codigo = gerarCodigo($tipo);
            $stmt = $pdo->prepare("INSERT INTO senhas (tipo, codigo, servico, status) VALUES (?, ?, ?, 'aguardando')");
            $stmt->execute([$tipo, $codigo, $tipo]);

            $urlImpressao = "imprimir_senha.php?tipo=" . urlencode($tipo) . "&codigo=" . urlencode($codigo);
        }
    }

    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Cliente - Tirar Senha</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            :root {
                --bg1: #e0f2fe;
                --bg2: #f8fafc;
                --primary: #1d4ed8;
                --success: #10b981;
                --warning: #f59e0b;
                --dark: #0f172a;
                --muted: #475569;
            }

            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, var(--bg1) 0%, var(--bg2) 100%);
                font-family: 'Segoe UI', Arial, sans-serif;
            }

            .kiosk-shell {
                width: min(1100px, 92vw);
                background: rgba(255, 255, 255, 0.8);
                border: 1px solid rgba(148, 163, 184, 0.25);
                border-radius: 28px;
                box-shadow: 0 30px 80px rgba(15, 23, 42, 0.12);
                padding: 2rem;
            }

            .titulo {
                text-align: center;
                margin-bottom: 1.5rem;
            }

            .titulo h2 {
                font-size: clamp(2rem, 3vw, 3rem);
                font-weight: 800;
                color: var(--dark);
                margin: 0;
            }

            .titulo p {
                margin: 0.6rem 0 0;
                color: var(--muted);
                font-size: 1.08rem;
            }

            .service-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(180px, 1fr));
                gap: 1.4rem;
                margin-top: 1.5rem;
            }

            .service-btn {
                min-height: 210px;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                border-radius: 24px;
                font-size: clamp(1.15rem, 1.8vw, 1.7rem);
                font-weight: 800;
                letter-spacing: 0.02em;
                border: none;
                color: #fff;
                box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                padding: 1.2rem;
            }

            .service-btn:hover {
                transform: translateY(-4px) scale(1.01);
                box-shadow: 0 22px 40px rgba(15, 23, 42, 0.18);
            }

            .btn-geral { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
            .btn-isencao { background: linear-gradient(135deg, #10b981, #059669); }
            .btn-prioridade { background: linear-gradient(135deg, #f59e0b, #d97706); }

            .footer-actions {
                margin-top: 1.6rem;
                display: flex;
                justify-content: center;
            }

            .logout-btn {
                background: rgba(239, 68, 68, 0.1);
                color: #b91c1c;
                border: 1px solid rgba(239, 68, 68, 0.35);
                border-radius: 14px;
                padding: 0.8rem 1.5rem;
                font-weight: 700;
                text-decoration: none;
            }

            @media (max-width: 768px) {
                .service-grid {
                    grid-template-columns: 1fr;
                }

                .service-btn {
                    min-height: 150px;
                }
            }
        </style>
    </head>
    <body>
    <div class="kiosk-shell">
        <?php if (!$licencaValida): ?>
            <div class="alert alert-warning border-0 rounded-4 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <strong>Licença anual necessária.</strong>
                        <div class="small mt-1">Ative a licença para continuar usando a kiok por 1 ano.</div>
                    </div>
                    <span class="badge bg-danger">Sem licença</span>
                </div>

                <?php if ($mensagemLicenca): ?>
                    <div class="alert alert-light mt-3 mb-0">
                        <?= htmlspecialchars($mensagemLicenca) ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="row g-2 mt-3 align-items-end">
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="licenca_codigo" placeholder="Digite o código da licença" required>
                    </div>
                    <div class="col-md-3 d-grid">
                        <button type="submit" class="btn btn-primary">Ativar</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-success border-0 rounded-4 mb-4">
                <strong>Licença ativa.</strong>
                <span class="ms-2">Válida até <?= date('d/m/Y', strtotime($licencaAtual['data_validade'])) ?></span>
            </div>
        <?php endif; ?>

        <div class="titulo">
            <h2>Bem-vindo, <?= htmlspecialchars($_SESSION['nome'] ?? 'Cliente') ?></h2>
            <p>Escolha o serviço desejado para retirar sua senha.</p>
        </div>

        <form method="post" class="service-grid" id="form-retirar-senha">
            <?php foreach ($servicosKiosk as $servico => $letra): ?>
                <?php $classe = ['G' => 'btn-geral', 'I' => 'btn-isencao', 'P' => 'btn-prioridade'][$letra]; ?>
                <button type="submit" name="tipo" value="<?= htmlspecialchars($servico) ?>" class="service-btn <?= $classe ?>"><?= htmlspecialchars($servico) ?></button>
            <?php endforeach; ?>
        </form>

        <?php if ($urlImpressao): ?>
            <p id="aviso-impressao" role="status" hidden>Senha criada. <a href="<?= htmlspecialchars($urlImpressao) ?>" target="_blank" rel="noopener">Abrir impressão</a>.</p>
            <script>
                const urlImpressao = <?= json_encode($urlImpressao, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
                const janelaImpressao = window.open(urlImpressao, 'impressaoSenha', 'width=600,height=600');
                if (janelaImpressao) {
                    janelaImpressao.focus();
                } else {
                    document.getElementById('aviso-impressao').hidden = false;
                }
            </script>
        <?php endif; ?>

        <script>
            document.getElementById('form-retirar-senha').addEventListener('submit', (evento) => {
                if (evento.submitter && evento.submitter.name === 'tipo') {
                    window.open('about:blank', 'impressaoSenha', 'width=600,height=600');
                }
            });
        </script>

        <div class="footer-actions">
            <a href="logout.php" class="logout-btn">Sair</a>
        </div>
    </div>
    </body>
    </html>
    <?php exit();
}

// === ATENDENTE ===
$servicosMenu = $servicosKiosk;
$servicosParaConsulta = expandirServicosKiosk(array_keys($servicosMenu));
$proximaSenha = null;
$mensagem = null;
$servicoSelecionadoAtual = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['chamar']) || isset($_POST['chamar_personalizado'])) {
        $servicoSelecionado = trim((string)($_POST['servico_personalizado'] ?? ''));
        $chamadaPorServico = isset($_POST['chamar_personalizado']);
        $servicoSelecionadoAtual = $chamadaPorServico ? $servicoSelecionado : '';
        $servicosParaChamada = $chamadaPorServico
            ? (isset($servicosMenu[$servicoSelecionado]) ? expandirServicosKiosk([$servicoSelecionado]) : [])
            : $servicosParaConsulta;

        if ($servicosParaChamada) {
            $placeholdersChamada = implode(',', array_fill(0, count($servicosParaChamada), '?'));
            $stmt = $pdo->prepare("SELECT * FROM senhas WHERE servico IN ($placeholdersChamada) AND status = 'aguardando' ORDER BY id ASC LIMIT 1");
            $stmt->execute($servicosParaChamada);
            $proximaSenha = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($proximaSenha) {
                $stmt = $pdo->prepare("UPDATE senhas SET status = 'chamado', visivel_painel = 1, atendente = ?, balcao = ?, chamado_em = NOW() WHERE id = ?");
                $stmt->execute([$dadosUsuario['nome'], $dadosUsuario['balcao'], $proximaSenha['id']]);
                $mensagem = "Senha chamada com sucesso!";
            } else {
                $mensagem = "Nenhuma senha disponível no momento.";
            }
        } elseif ($chamadaPorServico) {
            $mensagem = 'Selecione um serviço válido para chamar a senha.';
        } else {
            $mensagem = "Nenhum serviço configurado para este atendente.";
        }
    }

    if (isset($_POST['relembrar']) && isset($_POST['senha_id'])) {
        $stmt = $pdo->prepare("UPDATE senhas SET status = 'chamado', visivel_painel = 1, atendente = ?, balcao = ?, chamado_em = NOW() WHERE id = ? AND status = 'chamado'");
        $stmt->execute([$dadosUsuario['nome'], $dadosUsuario['balcao'], $_POST['senha_id']]);
        $stmt = $pdo->prepare("SELECT * FROM senhas WHERE id = ?");
        $stmt->execute([$_POST['senha_id']]);
        $proximaSenha = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($proximaSenha && $proximaSenha['status'] === 'chamado') {
            $mensagem = "Senha chamada novamente.";
        } else {
            $proximaSenha = null;
            $mensagem = "Esta senha não está mais em atendimento.";
        }
    }

    if (isset($_POST['nao_apareceu']) && isset($_POST['senha_id'])) {
        $stmt = $pdo->prepare("UPDATE senhas SET status = 'nao_apareceu', visivel_painel = 0 WHERE id = ?");
        $stmt->execute([$_POST['senha_id']]);
        $mensagem = "Marcado como 'não apareceu'.";
    }

    if (isset($_POST['atendido']) && isset($_POST['senha_id'])) {
        $stmt = $pdo->prepare("UPDATE senhas SET status = 'atendido', visivel_painel = 0 WHERE id = ?");
        $stmt->execute([$_POST['senha_id']]);
        $proximaSenha = null;
        $mensagem = "Atendimento finalizado.";
    }

    if (isset($_POST['liberar_balcao']) && isset($_POST['senha_id'])) {
        $stmt = $pdo->prepare("UPDATE senhas SET visivel_painel = 0 WHERE id = ?");
        $stmt->execute([$_POST['senha_id']]);
        $proximaSenha = null;
        $mensagem = "Balcão liberado.";
    }
}

$senhasAguardando = [];
$estatisticasAtendente = ['aguardando' => 0, 'chamados' => 0, 'totalHoje' => 0];
if ($servicosParaConsulta) {
    $placeholdersConsulta = implode(',', array_fill(0, count($servicosParaConsulta), '?'));
    $stmt = $pdo->prepare("SELECT * FROM senhas WHERE status = 'aguardando' AND servico IN ($placeholdersConsulta) ORDER BY id ASC LIMIT 10");
    $stmt->execute($servicosParaConsulta);
    $senhasAguardando = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT SUM(status = 'aguardando') AS aguardando, SUM(status = 'chamado') AS chamados, SUM(DATE(data_criacao) = CURDATE()) AS totalHoje FROM senhas WHERE servico IN ($placeholdersConsulta)");
    $stmt->execute($servicosParaConsulta);
    $estatisticasAtendente = array_map('intval', $stmt->fetch(PDO::FETCH_ASSOC) ?: $estatisticasAtendente);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Atendente - <?= htmlspecialchars($dadosUsuario['nome']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <?php if (!$licencaValida): ?>
            <div class="alert alert-warning mb-4">
                <strong>Licença anual necessária.</strong>
                <div class="small mt-1">Ative a licença para continuar com o painel da atendente.</div>

                <?php if ($mensagemLicenca): ?>
                    <div class="alert alert-light mt-2 mb-0">
                        <?= htmlspecialchars($mensagemLicenca) ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="row g-2 mt-3 align-items-end">
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="licenca_codigo" placeholder="Digite o código da licença" required>
                    </div>
                    <div class="col-md-3 d-grid">
                        <button type="submit" class="btn btn-primary">Ativar</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-success mb-4">
                <strong>Licença ativa.</strong>
                <span class="ms-2">Válida até <?= date('d/m/Y', strtotime($licencaAtual['data_validade'])) ?></span>
            </div>
        <?php endif; ?>

        <h2 class="mb-3">Bem-vindo, <?= htmlspecialchars($dadosUsuario['nome']) ?></h2>
        <p><strong>Balcão:</strong> <?= htmlspecialchars($dadosUsuario['balcao']) ?></p>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="border rounded p-3 bg-light">
                    <small class="text-muted">Hoje</small>
                    <h4 class="mb-0" id="total-hoje"><?= $estatisticasAtendente['totalHoje'] ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 bg-light">
                    <small class="text-muted">Aguardando</small>
                    <h4 class="mb-0" id="total-aguardando"><?= $estatisticasAtendente['aguardando'] ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 bg-light">
                    <small class="text-muted">Em atendimento</small>
                    <h4 class="mb-0" id="total-chamados"><?= $estatisticasAtendente['chamados'] ?></h4>
                </div>
            </div>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-info"><?= $mensagem ?></div>
        <?php endif; ?>

        <?php if ($proximaSenha): ?>
            <div class="alert alert-success text-center">
                <h1><?= htmlspecialchars($proximaSenha['codigo']) ?></h1>
                <p><?= htmlspecialchars($proximaSenha['tipo']) ?> - <?= htmlspecialchars($proximaSenha['servico']) ?></p>
                <form method="post">
                    <input type="hidden" name="senha_id" value="<?= $proximaSenha['id'] ?>">
                    <button type="submit" name="relembrar" class="btn btn-secondary">Relembrar</button>
                    <button type="submit" name="nao_apareceu" class="btn btn-warning">Não Apareceu</button>
                    <button type="submit" name="atendido" class="btn btn-success">Atendido</button>
                    <button type="submit" name="liberar_balcao" class="btn btn-danger">Liberar Balcão</button>
                </form>
            </div>
        <?php endif; ?>

        <form method="post" class="d-flex gap-2 flex-wrap mb-3" id="form-chamada-servico">
            <button type="submit" name="chamar" class="btn btn-primary flex-grow-1">Chamar Próxima Senha</button>

            <select name="servico_personalizado" id="servico-personalizado" class="form-select">
                <option value="">-- Escolher outro serviço --</option>
                <?php foreach ($servicosMenu as $valorServico => $rotuloServico): ?>
                    <option value="<?= htmlspecialchars($valorServico) ?>" <?= $servicoSelecionadoAtual === $valorServico ? 'selected' : '' ?>><?= htmlspecialchars($rotuloServico) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="chamar_personalizado" id="chamar-por-servico" class="btn btn-outline-primary" disabled>Chamar por Serviço</button>
        </form>
        <div id="previa-servico" class="alert alert-secondary" aria-live="polite" hidden>
            <strong id="previa-servico-nome"></strong>
            <div id="previa-servico-resultado">Selecione um serviço para consultar a próxima senha.</div>
        </div>

        <h5>Senhas aguardando:</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Serviço</th>
                    <th>Status</th>
                    <th>Balcão</th>
                    <th>Atendente</th>
                </tr>
                </thead>
                <tbody id="senhas-aguardando">
                <?php foreach ($senhasAguardando as $senha): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($senha['codigo']) ?></strong></td>
                        <td><?= htmlspecialchars($senha['servico']) ?></td>
                        <td><?= htmlspecialchars($senha['status']) ?></td>
                        <td><?= htmlspecialchars($senha['balcao'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($senha['atendente'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="painel_atendimento.php" class="btn btn-danger mt-3">Sair</a>
    </div>
</div>
<script>
async function atualizarFilaAtendente() {
    try {
        const resposta = await fetch('api_fila_atendente.php', { cache: 'no-store' });
        if (!resposta.ok) return;

        const dados = await resposta.json();
        const corpoTabela = document.getElementById('senhas-aguardando');
        corpoTabela.replaceChildren();

        dados.senhas.forEach((senha) => {
            const linha = document.createElement('tr');
            [senha.codigo, senha.servico, senha.status, senha.balcao || '-', senha.atendente || '-'].forEach((valor, indice) => {
                const celula = document.createElement('td');
                if (indice === 0) {
                    const codigo = document.createElement('strong');
                    codigo.textContent = valor;
                    celula.append(codigo);
                } else {
                    celula.textContent = valor;
                }
                linha.append(celula);
            });
            corpoTabela.append(linha);
        });

        document.getElementById('total-hoje').textContent = dados.estatisticas.totalHoje;
        document.getElementById('total-aguardando').textContent = dados.estatisticas.aguardando;
        document.getElementById('total-chamados').textContent = dados.estatisticas.chamados;
    } catch (erro) {
        console.error('Erro ao atualizar fila:', erro);
    } finally {
        window.setTimeout(atualizarFilaAtendente, 3000);
    }
}

atualizarFilaAtendente();

const seletorServico = document.getElementById('servico-personalizado');
const botaoChamarServico = document.getElementById('chamar-por-servico');
const previaServico = document.getElementById('previa-servico');
const nomeServicoPrevia = document.getElementById('previa-servico-nome');
const resultadoPrevia = document.getElementById('previa-servico-resultado');
let consultaPrevia = 0;

async function carregarPreviaServico() {
    const servico = seletorServico.value;
    const consultaAtual = ++consultaPrevia;
    botaoChamarServico.disabled = true;

    if (!servico) {
        previaServico.hidden = true;
        return;
    }

    previaServico.hidden = false;
    previaServico.className = 'alert alert-secondary';
    nomeServicoPrevia.textContent = servico;
    resultadoPrevia.textContent = 'Buscando próxima senha...';

    try {
        const resposta = await fetch(`api_proxima_senha.php?servico=${encodeURIComponent(servico)}`, { cache: 'no-store' });
        const dados = await resposta.json();
        if (consultaAtual !== consultaPrevia) return;

        if (!resposta.ok) throw new Error(dados.erro || 'Não foi possível consultar a fila.');
        if (!dados.senha) {
            previaServico.className = 'alert alert-warning';
            resultadoPrevia.textContent = 'Nenhuma senha aguardando para este serviço.';
            return;
        }

        previaServico.className = 'alert alert-info';
        resultadoPrevia.textContent = `Próxima senha: ${dados.senha.codigo}`;
        botaoChamarServico.disabled = false;
    } catch (erro) {
        if (consultaAtual !== consultaPrevia) return;
        previaServico.className = 'alert alert-danger';
        resultadoPrevia.textContent = erro.message;
    }
}

seletorServico.addEventListener('change', carregarPreviaServico);
if (seletorServico.value) carregarPreviaServico();
</script>
</body>
</html>
