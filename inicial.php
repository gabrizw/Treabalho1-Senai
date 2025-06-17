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
$dados = file_exists("dados.json") ? json_decode(file_get_contents("dados.json"), true) : [];

$dias = [];
$quantidade_produzida = [];
$quantidade_refugo = [];
$retrabalho = [];
$horas_trabalhadas = [];
$taxa_producao = [];
$taxa_refugo = [];
$limite_retrabalhos = []; // Adicionado para evitar erro
foreach ($dados as $item) {
    $dias[] = $item['data'];
    $quantidade_produzida[] = (int)$item['quantidade_produzida'];
    $quantidade_refugo[] = (int)$item['quantidade_refugo'];
    $retrabalho[] = (int)$item['retrabalho'];
    $horas_trabalhadas[] = (float)$item['horas_trabalhadas'];
    $taxa_producao[] = round($item['taxa_producao'], 2);
    $taxa_refugo[] = round($item['taxa_refugo'], 2);
    // Preencher arrays auxiliares para filtragem e gráficos
    $limite_retrabalhos[] = round($item['quantidade_produzida'] * 0.2);
    $taxas_producao[] = round($item['taxa_producao'], 2);
    $taxas_refugo[] = round($item['taxa_refugo'], 2);
}
    $taxa_producao[] = round($item['taxa_producao'], 2);
    $taxa_refugo[] = round($item['taxa_refugo'], 2);



$json = file_get_contents("dados.json");
$filtrar = !empty($_GET['data_inicio']) && !empty($_GET['data_fim']);
$data_inicio = $_GET['data_inicio'] ?? null;
$data_fim = $_GET['data_fim'] ?? null;
if ($filtrar) {
    $filtrados = [
        'dias' => [],
        'quantidade_produzida' => [],
        'retrabalho' => [],
        'quantidade_refugo' => [],
        'horas_trabalhadas' => [],
        'taxa_producao' => [],
        'taxa_refugo' => [],
        'limite_retrabalhos' => [],
        'taxas_producao' => [],
        'taxas_refugo' => [],
    ];
    foreach ($dias as $i => $data) {
        if ($data >= $data_inicio && $data <= $data_fim) {
            $filtrados['dias'][] = $dias[$i];
            $filtrados['quantidade_produzida'][] = $quantidade_produzida[$i];
            $filtrados['retrabalho'][] = $retrabalho[$i];
            $filtrados['quantidade_refugo'][] = $quantidade_refugo[$i];
            $filtrados['horas_trabalhadas'][] = $horas_trabalhadas[$i];
            $filtrados['taxa_producao'][] = $taxa_producao[$i];
            $filtrados['taxa_refugo'][] = $taxa_refugo[$i];
            $filtrados['limite_retrabalhos'][] = $limite_retrabalhos[$i];
            $filtrados['taxas_producao'][] = $taxas_producao[$i];
            $filtrados['taxas_refugo'][] = $taxas_refugo[$i];
        }
    }
    $dias = $filtrados['dias'];
    $quantidade_produzida = $filtrados['quantidade_produzida'];
    $retrabalho = $filtrados['retrabalho'];
    $quantidade_refugo = $filtrados['quantidade_refugo'];
    $horas_trabalhadas = $filtrados['horas_trabalhadas'];
    $taxa_producao = $filtrados['taxa_producao'];
    $taxa_refugo = $filtrados['taxa_refugo'];
    $limite_retrabalhos = $filtrados['limite_retrabalhos'];
    $taxas_producao = $filtrados['taxas_producao'];
    $taxas_refugo = $filtrados['taxas_refugo'];
}
    $vendas = $filtrados['vendas'] ?? [];
    $retrabalhos = $filtrados['retrabalhos'] ?? [];
    $perdas = $filtrados['perdas'] ?? [];
    $refugos = $filtrados['refugos'] ?? [];
    $taxas_producao = $filtrados['taxas_producao'] ?? [];
    $taxas_refugo = $filtrados['taxas_refugo'] ?? [];







?>
<!DOCTYPE html>
<html lang="pt-br">
<meta name="viewport">
<head>
    <meta charset="UTF-8">
    <title>Inicial</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                height: 80vh;
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
                height: 60vh;
            }
        }
        .text-white.me-3 {
            display: flex;
            align-items: center;
        }
        table th, table td {
            color: #333;
        }
        .btn.btn-primary { background: #e0bb9c; border-color: #e0bb9c; border-radius: 8px; }
        html ::-webkit-scrollbar {
        width: 10px;
        }

        html ::-webkit-scrollbar-thumb {
        border-radius: 50px;
        background: #e0bb9c;
        }

        html ::-webkit-scrollbar-track {
        background: #f7eee6;
        }
        .form-control {
            background: #f7eee6;
            border-color: #e0bb9c;
            position: relative;
            color: #e0bb9c;
        }
        .brand{
            font-family:Merriweather;
        }
        .row.mt-4.justify-content-center{
            width: 100%;
            height: 70vh;
            max-width: 100%;
            display: flex;
            justify-content: center;
        }
    </style>

</head>
<body>
    <nav class="navbar-custom d-flex justify-content-between align-items-center">
        <div class="brand">
            <a href="inicial.php" class="text-white me-3" style="color:#fff;text-decoration: none;">SAPATARIA</a>
        </div>
        <div class="d-flex align-items-center">
            <a href="inicial.php" class="me-3" style="color:#fff;text-decoration: none;">INICIO</a>
            <a href="registrodedados.php" class="me-3" style="color:#fff;text-decoration: none;">REGISTRAR DADOS</a>
            <a href="dados.php" class="me-3" style="color:#fff;text-decoration: none;">DADOS</a>
        <div class="user">
            <?php echo $nomes[$id]; ?> | <a href="sair.php" style="color:#fff;text-decoration:none;">SAIR</a>
        </div>
    </nav>
        
    <div class="container">
         <form method="get" class="mb-3">
    <div class="input-group">
        <input type="date" name="data_inicio" class="form-control" style="max-width: 150px;" value="<?php echo htmlspecialchars($_GET['data_inicio'] ?? ''); ?>">
        <input type="date" name="data_fim" class="form-control" style="max-width: 150px;" value="<?php echo htmlspecialchars($_GET['data_fim'] ?? ''); ?>">
        <button class="btn btn-primary" type="submit">Buscar</button>
        <?php if (!empty($_GET['data_inicio']) || !empty($_GET['data_fim'])): ?>
            <a href="inicial.php" class="btn btn-secondary">Limpar</a>
        <?php endif; ?>
    </div>
</form>
        <div class="row g-4">
            <div class="col-12 col-md-3">
                <div class="card text-center">
                    <div class="card-header">Vendas</div>
                    <div class="card-body">
                        <div class="stat"><?php echo array_sum($quantidade_produzida); ?></div>
                        <div class="stat-label">Total na semana</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-header">Tolerância de retrabalho</div>
                    <div class="card-body">
                        <div class="stat">2%</div>
                        <div class="stat-label">Tolerância de retrabalho</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-header">Trabalhadores</div>
                    <div class="card-body">
                        <div class="stat">8</div>
                        <div class="stat-label">Trabalhadores</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card text-center">
                    <div class="card-header">Retrabalhos</div>
                    <div class="card-body">
                        <div class="stat"><?php echo array_sum($retrabalho); ?></div>
                        <div class="stat-label">20% das vendas (Limite: <?php echo array_sum($limite_retrabalhos); ?>)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header text-center">
                        Gráfico de Vendas, Ganhos, Perdas e Retrabalhos
                    </div>
                    <div class="card-body">
                        <canvas id="dashboardChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header text-center">
                        Gráfico de Gastos Diários
                    </div>
                    <div class="card-body">
                        <canvas id="gastosChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header text-center">
                        Gráfico de Refugos Perdidos
                    </div>
                    <div class="card-body">
                        <canvas id="refugosChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header text-center">Tabela de Dados e Indicadores</div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped text-center">
                                <?php foreach ($dias as $i => $dias): ?>
                                    <tr>
                                        <td><?= $dias ?></td>
                                        <td><?= $quantidade_produzida[$i] ?? '' ?></td>
                                        <td><?= $retrabalho[$i] ?? '' ?></td>
                                        <td><?= $quantidade_refugo[$i] ?? '' ?></td>
                                        <td><?= $horas_trabalhadas[$i] ?? '' ?></td>
                                        <td><?= $taxa_producao[$i] ?? '' ?>%</td>
                                        <td><?= $taxa_refugo[$i] ?? '' ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            <tbody>
                                <?php foreach ($dias as $i => $dias): ?>
                                    <tr>
                                        <td><?= $dias ?></td>
                                        <td><?= $quantidade_produzida[$i] ?></td>
                                        <td><?= $quantidade_refugo[$i] ?></td>
                                        <td><?= $retrabalho[$i] ?></td>
                                        <td><?= $horas_trabalhadas[$i] ?></td>
                                        <td><?= $taxa_producao[$i] ?>%</td>
                                        <td><?= $taxas_refugo[$i] ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('dashboardChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dias); ?>,
                datasets: [
                    {
                        label: 'Vendas',
                        data: <?php echo json_encode($quantidade_produzida); ?>,
                        borderColor: '#57abf1',
                        backgroundColor: 'rgba(224,187,156,0.2)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Perdas',
                        data: <?php echo json_encode($quantidade_refugo); ?>,
                        borderColor: '#e92e42',
                        backgroundColor: 'rgba(232,204,181,0.2)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Retrabalhos (20%)',
                        data: <?php echo json_encode($retrabalho); ?>,
                        borderColor: '#49226e',
                        backgroundColor: 'rgba(255,111,97,0.1)',
                        tension: 0.3,
                        fill: true
                    },
                ]
            },
            options: {
                plugins: {
                    legend: {
                        labels: { color: '#e0bb9c', font: { size: 14 } }
                    }
                },
                scales: {
                    x: { ticks: { color: '#e0bb9c' } },
                    y: { ticks: { color: '#e0bb9c' } }
                }
            }
        });

        const ctxGastos = document.getElementById('gastosChart').getContext('2d');
        const gastosChart = new Chart(ctxGastos, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($dias); ?>,
                datasets: [{
                    label: 'Gastos Diários (R$)',
                    data: <?php echo json_encode([80, 95, 70, 110, 90, 85, 100]); ?>,
                    backgroundColor: 'rgba(233,46,66,0.5)',
                    borderColor: '#e92e42',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: { color: '#e0bb9c', font: { size: 14 } }
                    }
                },
                scales: {
                    x: { ticks: { color: '#e0bb9c' } },
                    y: { ticks: { color: '#e0bb9c' } }
                }
            }
        });

        const ctxRefugos = document.getElementById('refugosChart').getContext('2d');
        const refugosChart = new Chart(ctxRefugos, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($dias); ?>,
                datasets: [{
                    label: 'Refugos Perdidos',
                    data: <?php echo json_encode($quantidade_refugo); ?>,
                    backgroundColor: 'rgba(73,34,110,0.5)',
                    borderColor: '#49226e',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: { color: '#e0bb9c', font: { size: 14 } }
                    }
                },
                scales: {
                    x: { ticks: { color: '#e0bb9c' } },
                    y: { ticks: { color: '#e0bb9c' } }
                }
            }
        });
    </script>
    <div class="row mt-4 justify-content-center">
        <div class="col-12 col-md-6">
            <div class="card mb-4">
                <div class="card-header text-center">
                    Gráfico de Taxa de Produção
                </div>
                <div class="card-body">
                    <canvas id="taxaProducaoChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script>
        const ctxTaxaProducao = document.getElementById('taxaProducaoChart').getContext('2d');
        const taxaProducaoChart = new Chart(ctxTaxaProducao, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dias); ?>,
                datasets: [{
                    label: 'Taxa de Produção (%)',
                    data: <?php echo json_encode($taxa_producao); ?>,
                    borderColor: '#ff9800',
                      backgroundColor: 'rgba(255,152,0,0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: { color: '#e0bb9c', font: { size: 14 } }
                    }
                },
                scales: {
                    x: { ticks: { color: '#e0bb9c' } },
                    y: { 
                        ticks: { color: '#e0bb9c' },
                        beginAtZero: true,
                        max: 2000
                    }
                }
            }
        });
    </script>
</body>
</html>
