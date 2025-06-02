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
        $generos = json_decode(file_get_contents("genero.json"), true);
        $id = array_search($_SESSION['usuario'], $emails);
        $_SESSION['nomes'] = $nomes;
        $_SESSION['emails'] = $emails;
        $_SESSION['generos'] = $generos;
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
    @media (max-width: 768px) { .navbar-custom { padding: 1rem; } }
</style>

</head>
<body>
    <nav class="navbar-custom d-flex justify-content-between align-items-center">
        <div class="brand">SAPATARIA</div>
        <div class="user">
            <?php echo $nomes[$id]; ?> | <a href="sair.php" style="color:#fff;text-decoration:underline;">SAIR</a>
        </div>
    </nav>
    <div class="container">
        <div class="row g-4">
            <div class="col-12 col-md-3">
                <div class="card text-center">
                    <div class="card-header">Vendas</div>
                    <div class="card-body">
                        <div class="stat"><?php echo array_sum($vendas); ?></div>
                        <div class="stat-label">Total na semana</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-header">Ganhos</div>
                    <div class="card-body">
                        <div class="stat">R$ <?php echo number_format(array_sum($ganhos),2,',','.'); ?></div>
                        <div class="stat-label">Total na semana</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-header">Perdas</div>
                    <div class="card-body">
                        <div class="stat">R$ <?php echo number_format(array_sum($perdas),2,',','.'); ?></div>
                        <div class="stat-label">Total na semana</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card text-center">
                    <div class="card-header">Retrabalhos</div>
                    <div class="card-body">
                        <div class="stat"><?php echo array_sum($retrabalhos); ?></div>
                        <div class="stat-label">20% das vendas (Limite: <?php echo array_sum($limite_retrabalhos); ?>)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header text-center">
                Gráfico de Vendas, Ganhos, Perdas e Retrabalhos
            </div>
            <div class="card-body">
                <canvas id="dashboardChart" height="100"></canvas>
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
                        data: <?php echo json_encode($vendas); ?>,
                        borderColor: '#57abf1',
                        backgroundColor: 'rgba(224,187,156,0.2)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Ganhos',
                        data: <?php echo json_encode($ganhos); ?>,
                        borderColor: '#07cb59',
                        backgroundColor: 'rgba(240,221,206,0.2)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Perdas',
                        data: <?php echo json_encode($perdas); ?>,
                        borderColor: '#e92e42',
                        backgroundColor: 'rgba(232,204,181,0.2)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Retrabalhos (20%)',
                        data: <?php echo json_encode($retrabalhos); ?>,
                        borderColor: '#49226e',
                        backgroundColor: 'rgba(255,111,97,0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Limite Suportado (2%)',
                        data: <?php echo json_encode($limite_retrabalhos); ?>,
                        borderColor: '#ffff82',
                        backgroundColor: 'rgba(51,51,51,0.1)',
                        tension: 0.3,
                        fill: true
                    }
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
    </script>
</body>
</html>
 
