<?php  
$host = 'localhost';
$db   = 'atendimento_novo'; // Nome do banco correto
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança exceções em erros
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna resultados como arrays associativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepared statements reais
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit('Erro ao conectar ao banco de dados: ' . $e->getMessage());
}

function normalizarCodigoLicenca(string $codigo): string
{
    $codigo = trim($codigo);
    $codigo = preg_replace('/\s+/', '-', strtoupper($codigo));
    $codigo = preg_replace('/[^A-Z0-9-]/', '', $codigo);

    if ($codigo === '') {
        return 'KIOK-' . date('Y');
    }

    return $codigo;
}

function garantirTabelaLicenca(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS licencas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codigo VARCHAR(255) NOT NULL,
        ativa TINYINT(1) NOT NULL DEFAULT 1,
        data_inicio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        data_validade DATETIME NOT NULL,
        criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function buscarLicencaValida(PDO $pdo): ?array
{
    garantirTabelaLicenca($pdo);

    $stmt = $pdo->query("SELECT * FROM licencas ORDER BY id DESC LIMIT 1");
    $licenca = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;

    if (!$licenca) {
        return null;
    }

    $agora = new DateTimeImmutable('now');
    $validade = new DateTimeImmutable($licenca['data_validade']);

    if ($agora > $validade) {
        $pdo->prepare("UPDATE licencas SET ativa = 0 WHERE id = ?")->execute([$licenca['id']]);
        return null;
    }

    return $licenca;
}

function ativarLicenca(PDO $pdo, string $codigo): array
{
    garantirTabelaLicenca($pdo);

    $codigo = normalizarCodigoLicenca($codigo);
    if (strlen($codigo) < 8) {
        return ['sucesso' => false, 'mensagem' => 'A licença precisa ter pelo menos 8 caracteres válidos.'];
    }

    $stmt = $pdo->prepare("INSERT INTO licencas (codigo, ativa, data_inicio, data_validade) VALUES (?, 1, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR))");
    try {
        $stmt->execute([$codigo]);
        return ['sucesso' => true, 'mensagem' => 'Licença ativada com sucesso por 1 ano.'];
    } catch (PDOException $e) {
        return ['sucesso' => false, 'mensagem' => 'Não foi possível salvar a licença no momento.'];
    }
}
?>
