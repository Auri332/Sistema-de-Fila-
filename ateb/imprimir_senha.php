<?php
$tipo = $_GET['tipo'] ?? 'Desconhecido';
$codigo = $_GET['codigo'] ?? '---';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Imprimir Senha</title>
    <style>
        @media print {
            body * { visibility: hidden; }
            #conteudo, #conteudo * { visibility: visible; }
        }
        body {
            text-align: center;
            font-family: 'Arial', sans-serif;
            padding: 30px;
        }
        #conteudo {
            margin-top: 50px;
        }
        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
        }
        .tipo {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .codigo {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .balcao {
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div id="conteudo">
        <img src="logo.png" alt="Logo" class="logo">
        <div class="tipo"><?= htmlspecialchars($tipo) ?></div>
        <div class="codigo"><?= htmlspecialchars($codigo) ?></div>
        <div class="balcao">Aguardar chamada</div>
    </div>
    <script>
        window.addEventListener('afterprint', () => window.close(), { once: true });
        window.addEventListener('load', () => window.print(), { once: true });
    </script>
</body>
</html>
