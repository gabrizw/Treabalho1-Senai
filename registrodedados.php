<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');
$data_hora_sp = date('d-m-y h:i:s');

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

function calcularTaxaProducao($quantidade_produzida, $meta) {
    if ($meta == 0) return 0;
    return ($quantidade_produzida / $meta) * 100;
}

function calcularTaxaRefugo($quantidade_refugo, $quantidade_produzida) {
    if ($quantidade_produzida == 0) return 0;
    return ($quantidade_refugo / $quantidade_produzida) * 100;
}

function calcularTempoMedioProducao($quantidade_produzida, $horas_trabalhadas) {
    if ($horas_trabalhadas == 0) return 0;
    return $quantidade_produzida / $horas_trabalhadas;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dados = file_exists("dados.json") ? json_decode(file_get_contents("dados.json"), true) : [];

    $novo_registro = [
        "data" => $data_hora_sp,
        "quantidade_produzida" => $_POST["quantidade_produzida"],
        "quantidade_refugo" => $_POST["quantidade_refugo"],
        "retrabalho" => $_POST["retrabalho"],
        "horas_trabalhadas" => $_POST["horas_trabalhadas"]
    ];


    $novo_registro["taxa_producao"] = calcularTaxaProducao($novo_registro["quantidade_produzida"], $novo_registro["meta"]);
    $novo_registro["taxa_refugo"] = calcularTaxaRefugo($novo_registro["quantidade_refugo"], $novo_registro["quantidade_produzida"]);
    $novo_registro["tempo_medio_producao"] = calcularTempoMedioProducao($novo_registro["quantidade_produzida"], $novo_registro["horas_trabalhadas"]);

    $dados[] = $novo_registro;

    file_put_contents("dados.json", json_encode($dados, JSON_PRETTY_PRINT));
    header("Location: dados.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Dados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7eee6; color: #fff; font-family: 'Segoe UI', Arial, sans-serif; }
        .navbar-custom { background: #e0bb9c; padding: 1rem 2rem; border-radius: 0 0 16px 16px; margin-bottom: 2rem; box-shadow: 0 2px 8px #0002; }
        .navbar-custom .brand { font-size: 1.5rem; font-weight: bold; color: #fff; letter-spacing: 2px; }
        .navbar-custom .user { color: #fff; font-weight: 500; }
        .dados-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2.5rem 2rem;
            max-width: 400px;
            width: 100%;
        }
        html, body {
            height: 100vh;
            overflow: hidden;
        }
        .dados-card .icon {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .dados-card h2 {
            text-align: center;
            color: #e0bb9c;
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        .form-label {
            color: #a88b6b;
            font-weight: 500;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #e0bb9c;
            background: #f7eee6;
            color: #6d4c2b;
        }
        .form-control:focus {
            border-color: #e0bb9c;
            box-shadow: 0 0 0 0.1rem #e0bb9c33;
        }
        .btn-dados {
            background: #e0bb9c;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 1rem;
            transition: background 0.2s;
        }
        .btn-dados:hover {
            background: #cfa77e;
        }
        ::placeholder {
            color: #bfa88b;
        }
        @media (max-width: 576px) {
            .navbar-custom { flex-direction: column; text-align: center; }
            .brand { font-size: 1rem; }
            .dados-card { padding: 1.5rem 0.5rem 1rem 0.5rem; }
            .dados-card h2 { font-size: 1.1rem; }
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
    <div class="container d-flex justify-content-center align-items-center min-vh-150" style="background: none;">
        <div class="w-100 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
            <div class="dados-card">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#bfa88b" class="bi bi-card-text" viewBox="0 0 16 16">
                        <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
                        <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8m0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5"/>
                    </svg>
                </div>
                <h2>Registro de Produção</h2>
                <form action="registrodedados.php" method="POST">
                    <div class="mb-3">
                        <label for="quantidade_produzida" class="form-label">Quantidade Produzida</label>
                        <input type="number" class="form-control" id="quantidade_produzida" name="quantidade_produzida" required placeholder="Digite a quantidade produzida">
                    </div>
                    <div class="mb-3">
                        <label for="quantidade_refugo" class="form-label">Quantidade de Refugo</label>
                        <input type="number" class="form-control" id="quantidade_refugo" name="quantidade_refugo" required placeholder="Digite a quantidade de refugo">
                    </div>
                    <div class="mb-3">
                        <label for="Retrabalho" class="form-label">Retrabalho</label>
                        <input type="number" class="form-control" id="Retrabalho" name="Retrabalho" required placeholder="Digite a quantidade de Retrabalho">
                    </div>
                    <div class="mb-3">
                        <label for="horas_trabalhadas" class="form-label">Horas Trabalhadas</label>
                        <input type="number" step="0.1" class="form-control" id="horas_trabalhadas" name="horas_trabalhadas" required placeholder="Digite as horas trabalhadas">
                    </div>
                    <button type="submit" class="btn btn-dados w-100">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
