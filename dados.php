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
    }
    else {
        $emails = $_SESSION['emails'];
        $id = array_search($_SESSION['usuario'], $emails);
        $nomes = $_SESSION['nomes'];
    }
$vendas = [140, 160, 130, 150, 170, 120, 130];
$ganhos = array_map(fn($v) => $v * 50, $vendas);
$perdas = [10, 12, 8, 15, 9, 11, 10];
$dias = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];

$retrabalhos = array_map(fn($v) => round($v * 0.2), $vendas); 
$limite_retrabalhos = array_map(fn($v) => round($v * 0.02), $vendas); 

$retrabalhos = array_map(fn($v) => round($v * 0.2), $vendas);
$limite_retrabalhos = array_map(fn($v) => round($v * 0.02), $vendas);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="content-language" content="pt-br">
    <meta charset="UTF-8">
    <title>Registro de Dados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7eee6; color: #fff; font-family: 'Segoe UI', Arial, sans-serif; }
        .navbar-custom { background: #e0bb9c; padding: 1rem 2rem; border-radius: 0 0 16px 16px; margin-bottom: 2rem; box-shadow: 0 2px 8px #0002; }
        .navbar-custom .brand { font-size: 1.5rem; font-weight: bold; color: #fff; letter-spacing: 2px; }
        .navbar-custom .user { color: #fff; font-weight: 500; }
        .card { background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); margin-bottom: 2rem; }
        .card-header { background: #e0bb9c; color: #fff; border-radius: 16px 16px 0 0; font-weight: bold; font-size: 1.2rem; }
        .stat { font-size: 2rem; color: #000; }
        .stat-label { color: #fff; font-size: 1rem; }
        .highlight { color: #fff; font-weight: bold; }
        canvas {
            width: 100%;
            height: 40vh;
            max-width: 100%;
            display: block;
        }
        @media (max-width: 768px) {
            .navbar-custom { padding: 1rem; }
            .card-body canvas {
                height: 50vh;
            }
        }
        @media (max-width: 576px) {
            .navbar-custom { flex-direction: column; text-align: center; }
            .brand { font-size: 1rem; }
            .card-header { font-size: 0.95rem; }
            .stat { font-size: 1.4rem; }
            .row.g-4 > [class^="col-"] { margin-bottom: 2rem; }
            .row.mt-4 { height: auto; } 
            .card-body canvas {
                height: 30vh;
            }
        }
        .dados-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 400px;
            width: 100%;
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
    </style>
</head>
<body>
    <nav class="navbar-custom d-flex justify-content-between align-items-center mb-4">
                <div class="brand">SAPATARIA</div>
                <div class="d-flex align-items-center">
                    <a href="inicial.php" class="me-3" style="color:#fff;text-decoration: none;">INICIO</a>
                    <a href="dados.php" class="me-3" style="color:#fff;text-decoration: none;">REGISTRAR DADOS</a>
                    <div class="user">
                        <?php if(isset($nomes[$id])) { echo $nomes[$id]; } ?> | <a href="sair.php" style="color:#fff;text-decoration:none;">SAIR</a>
                    </div>
                </div>
            </nav>
    <div class="container d-flex justify-content-center align-items-center min-vh-100" style="background: none;">
        <div>
            <div class="dados-card">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#bfa88b" class="bi bi-card-text" viewBox="0 0 16 16">
  <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
  <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8m0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5"/>
</svg>
                </div>
                <h2>Registro de Produção</h2>
                <form action="processar_dados.php" method="POST">
                    <div class="mb-3">
                        <label for="data" class="form-label">Data</label>
                        <input type="date" class="form-control" id="data" name="data" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantidade_produzida" class="form-label">Quantidade Produzida</label>
                        <input type="number" class="form-control" id="quantidade_produzida" name="quantidade_produzida" required placeholder="Digite a quantidade produzida">
                    </div>
                    <div class="mb-3">
                        <label for="quantidade_refugo" class="form-label">Quantidade de Refugo</label>
                        <input type="number" class="form-control" id="quantidade_refugo" name="quantidade_refugo" required placeholder="Digite a quantidade de refugo">
                    </div>
                    <div class="mb-3">
                        <label for="tempo_producao" class="form-label">Tempo de Produção (em minutos)</label>
                        <input type="number" class="form-control" id="tempo_producao" name="tempo_producao" required placeholder="Digite o tempo de produção">
                    </div>
                    <button type="submit" class="btn btn-dados w-100">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
