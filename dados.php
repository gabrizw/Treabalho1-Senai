<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
if (!isset($_SESSION['nomes'])) {
    $emails = json_decode(file_get_contents("email.json"), true);
    $senhas = json_decode(file_get_contents("senha.json"), true);
    $nomes = json_decode(file_get_contents("nome.json"), true);
    $id = array_search($_SESSION['usuario'], $emails);
    $_SESSION['nomes'] = $nomes;
    $_SESSION['emails'] = $emails;
    $_SESSION['senhas'] = $senhas;
} else {
    $emails = $_SESSION['emails'];
    $id = array_search($_SESSION['usuario'], $emails);
    $nomes = $_SESSION['nomes'];
}
$dados = json_decode(file_get_contents("dados.json"), true);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dados de Produção</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7eee6; color: #fff; font-family: 'Segoe UI', Arial, sans-serif; }
        .navbar-custom { background: #e0bb9c; padding: 1rem 2rem; border-radius: 0 0 16px 16px; margin-bottom: 2rem; box-shadow: 0 2px 8px #0002; }
        .navbar-custom .brand { font-size: 1.5rem; font-weight: bold; color: #fff; letter-spacing: 2px; }
        .navbar-custom .user { color: #fff; font-weight: 500; }
        .table-custom { background: #fff; border-radius: 16px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); }
        .table thead { background: #e0bb9c; color: #fff; }
        .table tbody tr td { color: #6d4c2b; }
        .table th, .table td { vertical-align: middle; }
        .dados-title { color: #e0bb9c; font-weight: bold; margin-bottom: 2rem; text-align: center; }
        @media (max-width: 576px) {
            .navbar-custom { flex-direction: column; text-align: center; }
            .brand { font-size: 1rem; }
            .dados-title { font-size: 1.1rem; }
        }
    </style>
</head>
<body>
    <nav class="navbar-custom d-flex justify-content-between align-items-center mb-4">
        <div class="brand">SAPATARIA</div>
        <div class="d-flex align-items-center">
            <a href="inicial.php" class="me-3" style="color:#fff;text-decoration: none;">INICIO</a>
            <a href="registrodedados.php" class="me-3" style="color:#fff;text-decoration: none;">REGISTRAR DADOS</a>
            <a href="dados.php" class="me-3" style="color:#fff;text-decoration: none;">DADOS</a>
            <div class="user">
                <?php if(isset($nomes[$id])) { echo $nomes[$id]; } ?> | <a href="sair.php" style="color:#fff;text-decoration:none;">SAIR</a>
            </div>
        </div>
    </nav>
    <div class="container mb-5">
        <h2 class="dados-title">Registros de Produção</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-custom">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Produzido</th>
                        <th>Refugo</th>
                        <th>Tempo (min)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dados)): ?>
                        <?php foreach ($dados as $registro): ?>
                            <tr>
                                <td><?= htmlspecialchars($registro["data"]) ?></td>
                                <td><?= htmlspecialchars($registro["quantidade_produzida"]) ?></td>
                                <td><?= htmlspecialchars($registro["quantidade_refugo"]) ?></td>
                                <td><?= htmlspecialchars($registro["tempo_producao"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Nenhum registro encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
