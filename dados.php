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
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$dados_filtrados = [];
if ($busca !== '' && !empty($dados)) {
    foreach ($dados as $registro) {
        if (
            stripos($registro["data"], $busca) !== false ||
            stripos($registro["quantidade_produzida"], $busca) !== false ||
            stripos($registro["quantidade_refugo"], $busca) !== false ||
            stripos($registro["tempo_producao"], $busca) !== false
        ) {
            $dados_filtrados[] = $registro;
        }
    }
} else {
    $dados_filtrados = $dados;
}

function calcularTaxaProducao($quantidade_produzida, $reproducao) {
    if ($reproducao == 0) return 0;
    return $quantidade_produzida / $reproducao;
}


function calcularTaxaRefugo($quantidade_refugo, $quantidade_produzida) {
    if ($quantidade_produzida == 0) return 0;
    return $quantidade_refugo / $quantidade_produzida;
}


$dados = [];
if (file_exists("dados.json")) {
    $dados = json_decode(file_get_contents("dados.json"), true);
    foreach ($dados as $k => $registro) {
        $taxa_producao = calcularTaxaProducao(
            $registro['quantidade_produzida'],
            $registro['reproducao']
        );
        $taxa_refugo = calcularTaxaRefugo(
            $registro['quantidade_refugo'],
            $registro['quantidade_produzida']
        );
        $dados[$k]['taxa_producao'] = $taxa_producao;
        $dados[$k]['taxa_refugo'] = $taxa_refugo;
    }

}



file_put_contents("dados.json", json_encode($dados, JSON_PRETTY_PRINT));
$json = file_get_contents("dados.json");
$dados = json_decode($json, true);
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
        .btn.btn-primary { background: #e0bb9c; border-color: #e0bb9c; }
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
        <form method="get" class="mb-3">
            <div class="input-group">
                <input type="text" name="busca" class="form-control" placeholder="Buscar por uma data" value="<?= htmlspecialchars($busca) ?>">
                <button class="btn btn-primary" type="submit">Buscar</button>
                <?php if($busca !== ''): ?>
                    <a href="dados.php" class="btn btn-secondary">Limpar</a>
                <?php endif; ?>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-custom">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Produzido</th>
                        <th>Refugo</th>
                        <th>Reproduções</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dados_filtrados)): ?>
                        <?php foreach ($dados_filtrados as $registro): ?>
                            <tr>
                                <td><?= htmlspecialchars($registro["data"]) ?></td>
                                <td><?= htmlspecialchars($registro["quantidade_produzida"]) ?></td>
                                <td><?= htmlspecialchars($registro["quantidade_refugo"]) ?></td>
                                <td><?= htmlspecialchars($registro["reproducao"]) ?></td>
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
