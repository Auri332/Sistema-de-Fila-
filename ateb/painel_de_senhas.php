<?php
// Arquivo: painel_exibicao.php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Painel de Exibição</title>
  <style>
    :root {
      --bg1: #0f172a;
      --bg2: #1d4ed8;
      --panel: rgba(15, 23, 42, 0.8);
      --panel-border: rgba(255,255,255,0.15);
      --title: #f8fafc;
      --subtitle: #dbeafe;
      --accent: #fbbf24;
      --accent-2: #34d399;
      --text: #e2e8f0;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      font-family: 'Segoe UI', Arial, sans-serif;
      background: linear-gradient(135deg, var(--bg1) 0%, var(--bg2) 100%);
      color: var(--text);
      overflow: hidden;
    }

    .painel {
      width: min(90vw, 900px);
      background: var(--panel);
      border: 1px solid var(--panel-border);
      border-radius: 26px;
      padding: 42px 52px;
      box-shadow: 0 30px 80px rgba(15, 23, 42, 0.45);
      backdrop-filter: blur(6px);
      position: relative;
    }

    .painel::before {
      content: "";
      position: absolute;
      inset: 14px;
      border: 2px solid rgba(255,255,255,0.08);
      border-radius: 20px;
    }

    #tipoServico {
      position: relative;
      z-index: 1;
      font-size: clamp(1.5rem, 2vw, 2.3rem);
      margin: 0 0 18px;
      color: var(--subtitle);
      letter-spacing: 0.08em;
      text-transform: uppercase;
      font-weight: 700;
    }

    #codigoSenha {
      position: relative;
      z-index: 1;
      margin: 10px 0 18px;
      font-size: clamp(4rem, 10vw, 10rem);
      font-weight: 900;
      line-height: 1;
      color: var(--accent);
      text-shadow: 0 0 25px rgba(251, 191, 36, 0.5);
    }

    #balcao {
      position: relative;
      z-index: 1;
      font-size: clamp(1.5rem, 2.2vw, 2.6rem);
      margin: 10px 0 0;
      color: var(--title);
      font-weight: 700;
    }

    #liberarBtn {
      position: relative;
      z-index: 1;
      margin-top: 30px;
      font-size: 1.1rem;
      padding: 14px 28px;
      background: linear-gradient(135deg, var(--accent-2), #22c55e);
      color: white;
      border: none;
      border-radius: 999px;
      cursor: pointer;
      font-weight: 700;
      box-shadow: 0 18px 30px rgba(34, 197, 94, 0.25);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    #liberarBtn:hover {
      transform: translateY(-2px);
      box-shadow: 0 22px 30px rgba(34, 197, 94, 0.35);
    }
  </style>
</head>
<body>

<div class="painel" id="painelSenha" style="display: none;">
  <h2 id="tipoServico">---</h2>
  <h1 id="codigoSenha">---</h1>
  <h2 id="balcao">---</h2>
  <button id="liberarBtn">Balcão liberado</button>
</div>

<script>
let ultimaSenhaId = null;
let ultimaChamada = null;

function falarSenha(data) {
  if (!('speechSynthesis' in window)) return;

  const texto = `${data.tipo || data.servico || 'Senha'}. Senha ${String(data.codigo || '').split('').join(' ')}. Dirija-se ao balcão ${data.balcao || ''}`;
  const repetir = 3;
  let indice = 0;

  window.speechSynthesis.cancel();

  function falarProxima() {
    if (indice >= repetir) return;

    const mensagem = new SpeechSynthesisUtterance(texto);
    mensagem.lang = 'pt-BR';
    mensagem.rate = 1;
    mensagem.pitch = 1;
    mensagem.onend = () => {
      indice += 1;
      if (indice < repetir) {
        setTimeout(falarProxima, 700);
      }
    };

    window.speechSynthesis.speak(mensagem);
  }

  falarProxima();
}

async function buscarSenha() {
  try {
    const res = await fetch('api_senha_chamada.php');
    const data = await res.json();

    if (!data || !data.id) return;

    const chamadaAtual = `${data.id}|${data.chamado_em || ''}`;
    if (chamadaAtual === ultimaChamada) return;

    ultimaSenhaId = data.id;
    ultimaChamada = chamadaAtual;
    document.getElementById('painelSenha').style.display = 'block';
    document.getElementById('tipoServico').textContent = (data.tipo || data.servico || 'Senha').toUpperCase();
    document.getElementById('codigoSenha').textContent = data.codigo;
    document.getElementById('balcao').textContent = 'Dirija-se ao balcão ' + (data.balcao || '');

    falarSenha(data);
  } catch (err) {
    console.error('Erro ao buscar senha:', err);
  }
}

document.getElementById('liberarBtn').addEventListener('click', async () => {
  if (!ultimaSenhaId) return;

  await fetch('api_liberar_balcao.php?id=' + ultimaSenhaId);
  document.getElementById('painelSenha').style.display = 'none';
  ultimaSenhaId = null;
  window.speechSynthesis.cancel();
});

setInterval(buscarSenha, 3000);
</script>

</body>
</html>
