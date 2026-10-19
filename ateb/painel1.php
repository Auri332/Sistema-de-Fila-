<?php   
require 'conexao.php';

$stmt = $pdo->query("SELECT * FROM senhas WHERE status = 'chamado' ORDER BY chamado_em DESC LIMIT 1");
$chamada = $stmt->fetch(PDO::FETCH_ASSOC);

$servico = $chamada['servico'] ?? '';
$codigo = $chamada['codigo'] ?? '';
$balcao = $chamada['balcao'] ?? '';
$senha_id = $chamada['id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel de Chamada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 30px;
            border-bottom: 2px solid #1f4fa0;
        }

        .logo img {
            height: 80px;
        }

        .titulo {
            flex: 1;
            text-align: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: #1f4fa0;
            text-transform: uppercase;
        }

        .container-painel {
            display: flex;
            height: calc(100vh - 100px);
            padding: 20px;
        }

        .lado-esquerdo {
            width: 50%;
            border: 2px solid #1f4fa0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .lado-direito {
            width: 50%;
            border: 2px solid #1f4fa0;
            display: flex;
            flex-direction: column;
            padding: 24px;
            overflow: auto;
        }

        .historico-titulo {
            color: #1f4fa0;
            font-size: 2.3rem;
            font-weight: 700;
            margin: 0 0 18px;
        }

        .historico-tabela {
            width: 100%;
            border-collapse: collapse;
        }

        .historico-tabela th,
        .historico-tabela td {
            padding: 16px 12px;
            border-bottom: 1px solid #d8e0eb;
            text-align: left;
            font-size: 1.35rem;
        }

        .historico-tabela th {
            color: #475569;
            font-size: 1.15rem;
            text-transform: uppercase;
        }

        .historico-tabela td:first-child {
            color: #1f4fa0;
            font-size: 1.7rem;
            font-weight: 700;
        }

        .historico-vazio {
            color: #64748b;
            padding: 22px 12px;
            text-align: center;
        }

        .servico {
            max-width: 100%;
            font-size: 2.8rem;
            color: #1f4fa0;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
            overflow-wrap: anywhere;
        }

        .codigo {
            font-size: 11rem;
            line-height: 1;
            font-weight: bold;
            color: #1f4fa0;
            margin: 20px 0;
        }

        .balcao {
            font-size: 2.8rem;
            color: #1f4fa0;
            font-weight: 700;
            text-align: center;
        }

        @media (max-width: 760px) {
            .container-painel {
                height: auto;
                min-height: calc(100vh - 100px);
                flex-direction: column;
            }

            .lado-esquerdo,
            .lado-direito {
                width: 100%;
                min-height: 45vh;
            }

            .servico,
            .balcao {
                font-size: 2rem;
            }

            .codigo {
                font-size: 6rem;
            }

            .historico-tabela {
                min-width: 620px;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">
            <img src="logo.png" alt="Logo Hospital">
        </div>
        <div class="titulo">Hospital Central do Lubango</div>
        <div style="width:80px;"></div> <!-- espaço para balancear layout -->
    </div>

    <div class="container-painel">
        <div class="lado-esquerdo">
            <div class="servico"><?= htmlspecialchars(strtoupper($servico)) ?></div>
            <div class="codigo"><?= htmlspecialchars($codigo) ?></div>
            <div class="balcao">BALCÃO <?= htmlspecialchars($balcao) ?></div>
        </div>

        <div class="lado-direito">
            <h2 class="historico-titulo">Últimas senhas chamadas</h2>
            <table class="historico-tabela">
                <thead>
                    <tr>
                        <th>Senha</th>
                        <th>Serviço</th>
                        <th>Hora</th>
                        <th>Balcão</th>
                    </tr>
                </thead>
                <tbody id="historico-chamadas">
                    <tr><td colspan="4" class="historico-vazio">Carregando chamadas...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let ultimaChamada = <?= json_encode($senha_id . '|' . ($chamada['chamado_em'] ?? '')) ?>;

        function falarSenha(data) {
            if (!window.speechSynthesis || !data.codigo) return;

            const servico = data.servico || data.tipo || 'Senha';
            const balcao = String(data.balcao || '').replace(/[^\d]/g, '');
            const frase = `${servico}, senha ${String(data.codigo).split('').join(' ')}, dirija-se ao balcão ${balcao}`;
            let repeticao = 0;
            window.speechSynthesis.cancel();

            function anunciar() {
                if (repeticao >= 3) return;
                const fala = new SpeechSynthesisUtterance(frase);
                fala.lang = 'pt-BR';
                fala.rate = 1;
                fala.pitch = 1;
                fala.onend = () => {
                    repeticao += 1;
                    if (repeticao < 3) window.setTimeout(anunciar, 700);
                };
                window.speechSynthesis.speak(fala);
            }

            anunciar();
        }

        function exibirSenha(senha) {
            document.querySelector('.servico').textContent = (senha.servico || senha.tipo || '').toUpperCase();
            document.querySelector('.codigo').textContent = senha.codigo || '';
            document.querySelector('.balcao').textContent = `BALCÃO ${senha.balcao || ''}`;
        }

        async function verificarChamada() {
            try {
                const resposta = await fetch(`painel_verifica.php?ultima=${encodeURIComponent(ultimaChamada)}`, { cache: 'no-store' });
                if (!resposta.ok) throw new Error('Falha ao consultar chamadas');
                const dados = await resposta.json();

                atualizarHistorico(dados.historico || []);

                if (dados.novaChamada && dados.senha) {
                    ultimaChamada = dados.senha.evento;
                    exibirSenha(dados.senha);
                    falarSenha(dados.senha);
                }
            } catch (erro) {
                console.error('Erro ao atualizar painel:', erro);
            } finally {
                window.setTimeout(verificarChamada, 3000);
            }
        }

        function atualizarHistorico(historico) {
            const corpo = document.getElementById('historico-chamadas');
            corpo.replaceChildren();

            if (!historico.length) {
                const linha = document.createElement('tr');
                const celula = document.createElement('td');
                celula.colSpan = 4;
                celula.className = 'historico-vazio';
                celula.textContent = 'Nenhuma senha chamada.';
                linha.append(celula);
                corpo.append(linha);
                return;
            }

            historico.forEach((senha) => {
                const linha = document.createElement('tr');
                [senha.codigo, senha.servico, senha.hora, senha.balcao ? `Balcão ${senha.balcao}` : '-'].forEach((valor) => {
                    const celula = document.createElement('td');
                    celula.textContent = valor || '-';
                    linha.append(celula);
                });
                corpo.append(linha);
            });
        }

        window.addEventListener('load', () => {
            if (<?= (int)$senha_id ?>) {
                const inicial = {
                    servico: document.querySelector('.servico').textContent.trim(),
                    codigo: document.querySelector('.codigo').textContent.trim(),
                    balcao: document.querySelector('.balcao').textContent.trim().replace(/[^\d]/g, '')
                };
                window.setTimeout(() => falarSenha(inicial), 500);
            }
            verificarChamada();
        });
    </script>

</body>
</html>
