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

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$dados = file_exists("dados.json") ? json_decode(file_get_contents("dados.json"), true) : [];

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



$dados_filtrados = [];
if ($busca !== '' && !empty($dados)) {
    foreach ($dados as $registro) {
        if (
            stripos($registro["data"], $busca) !== false ||
            stripos($registro["quantidade_produzida"], $busca) !== false ||
            stripos($registro["quantidade_refugo"], $busca) !== false
        ) {
            $dados_filtrados[] = $registro;
        }
    }
} else {
    $dados_filtrados = $dados;
}

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
        #pagination button{
            border-radius:8px;
            min-width: 40px;
        }
        .btn-outline-primary{
            background: #f7eee6;
            border-color: #e0bb9c;
            margin-bottom: 2px;
            transition: background 0.2s;
        }
        .btn-outline-primary:hover{
            background: #cfa77e;
        }
        .brand{
            font-family:Merriweather;
        }
        .btn.btn-sm.mx-1{
            border-color: #e0bb9c;
            color: #000
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
                <?= isset($nomes[$id]) ? $nomes[$id] : '' ?> | <a href="sair.php" style="color:#fff;text-decoration:none;">SAIR</a>
            </div>
        </div>
    </nav>
    <div class="container mb-5">
        <h2 class="dados-title">Registros de Produção</h2>
        <div class="container">
         <form method="get" class="mb-3">
    <div class="input-group">
        <input type="date" name="data_inicio" class="form-control" style="max-width: 150px;">
        <input type="date" name="data_fim" class="form-control" style="max-width: 150px;">
        <button class="btn btn-primary" type="submit">Buscar</button>
        <?php if ($busca !== ''): ?>
            <a href="inicial.php" class="btn btn-secondary">Limpar</a>
        <?php endif; ?>
    </div>
        <div class="table-responsive">
            <table class="table table-bordered table-custom">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Produzido</th>
                        <th>Refugo</th>
                        <th>Retrabalho</th>
                        <th>Horas Trabalhadas</th>
                        <th>Taxa de Produção (%)</th>
                        <th>Taxa de Refugo (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dados_filtrados)): ?>
                        <?php foreach ($dados_filtrados as $registro): ?>
                            <tr>
                                <td><?= htmlspecialchars($registro["data"]) ?></td>
                                <td><?= htmlspecialchars($registro["quantidade_produzida"]) ?></td>
                                <td><?= htmlspecialchars($registro["quantidade_refugo"]) ?></td>
                                <td><?= htmlspecialchars($registro["retrabalho"]) ?></td>
                                <td><?= htmlspecialchars($registro["horas_trabalhadas"]) ?></td>
                                <td><?= number_format($registro["taxa_producao"], 2) ?>%</td>
                                <td><?= number_format($registro["taxa_refugo"], 2) ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Nenhum registro encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
    const table = document.querySelector("table tbody");
    const rows = Array.from(table.querySelectorAll("tr"));
    const rowsPerPage = 10;
    let currentPage = 1;
    let currentSortColumn = null;
    let currentSortAsc = true;

    function paginate(rows, page, rowsPerPage) {
        const start = (page - 1) * rowsPerPage;
        return rows.slice(start, start + rowsPerPage);
    }

    function renderTable(dataRows) {
        table.innerHTML = "";
        dataRows.forEach(row => table.appendChild(row));
    }

    function updateTable() {
        const paginatedRows = paginate(rows, currentPage, rowsPerPage);
        renderTable(paginatedRows);
        renderPaginationControls();
    }

    function renderPaginationControls() {
        let pagination = document.getElementById("pagination");
        if (!pagination) {
            pagination = document.createElement("div");
            pagination.id = "pagination";
            pagination.className = "mt-3 d-flex justify-content-center";
            table.parentElement.appendChild(pagination);
        }
        pagination.innerHTML = "";

        const totalPages = Math.ceil(rows.length / rowsPerPage);
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement("button");
            btn.textContent = i;
            btn.className = "btn btn-sm mx-1 " + (i === currentPage ? "btn-primary" : "btn-outline-primary");
            btn.onclick = () => {
                currentPage = i;
                updateTable();
            };
            pagination.appendChild(btn);
        }
    }

    function sortTableByColumn(columnIndex) {
        const type = columnIndex === 0 ? "string" : "number";
        rows.sort((a, b) => {
            const aText = a.children[columnIndex].textContent.trim();
            const bText = b.children[columnIndex].textContent.trim();

            let aVal = type === "number" ? parseFloat(aText.replace(",", ".")) : aText;
            let bVal = type === "number" ? parseFloat(bText.replace(",", ".")) : bText;

            if (aVal < bVal) return currentSortAsc ? -1 : 1;
            if (aVal > bVal) return currentSortAsc ? 1 : -1;
            return 0;
        });

        currentSortAsc = (currentSortColumn === columnIndex) ? !currentSortAsc : true;
        currentSortColumn = columnIndex;
        currentPage = 1;
        updateTable();
    }


    document.querySelectorAll("thead th").forEach((th, index) => {
        th.style.cursor = "pointer";
        th.addEventListener("click", () => sortTableByColumn(index));
        
    });
    
    updateTable();
</script>
</body>
</html>
