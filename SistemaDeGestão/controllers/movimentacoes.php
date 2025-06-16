<?php
include('../config/conexao.php');

// Definir filtros com valores padrão
$dataInicial = $_GET['dataInicial'] ?? date('Y-m-d');
$dataFinal = $_GET['dataFinal'] ?? date('Y-m-d');
$status = $_GET['status'] ?? 'Todas';

// Montar consulta conforme filtros
if ($status != 'Todas') {
    $query = "SELECT * FROM movimentacoes 
              WHERE data BETWEEN '$dataInicial' AND '$dataFinal' 
              AND tipo = '$status' ORDER BY data ASC";
} else {
    $query = "SELECT * FROM movimentacoes 
              WHERE data BETWEEN '$dataInicial' AND '$dataFinal' 
              ORDER BY data ASC";
}

$result = mysqli_query($conexao, $query);
$row = mysqli_num_rows($result);

// Cálculo de totais
$queryEntradas = "SELECT SUM(valor) AS total FROM movimentacoes 
          WHERE data BETWEEN '$dataInicial' AND '$dataFinal' 
          AND tipo = 'Entrada'";

$querySaidas = "SELECT SUM(valor) AS total FROM movimentacoes 
        WHERE data BETWEEN '$dataInicial' AND '$dataFinal' 
        AND tipo = 'Saída'";

$resultEntradas = mysqli_query($conexao, $queryEntradas);
$resultSaidas = mysqli_query($conexao, $querySaidas);

$totalEntradas = mysqli_fetch_assoc($resultEntradas)['total'] ?? 0;
$totalSaidas = mysqli_fetch_assoc($resultSaidas)['total'] ?? 0;

// Aplica filtros manuais se necessário
if ($status == 'Entrada') $totalSaidas = 0;
if ($status == 'Saída') $totalEntradas = 0;

$saldo = $totalEntradas - $totalSaidas;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Movimentações</title>
    <link rel="stylesheet" href="../assets/cssClientes.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <header>
        <div class="voltar">
            <a href="../views/painel_tesouraria.php"><i class="fa-solid fa-arrow-left"></i></a>
        </div>
        <div class="pesquisar">
            <form class="campo-pesquisa" method="GET">
                <select name="status">
                    <option value="Todas" <?= $status == 'Todas' ? 'selected' : '' ?>>Todas</option>
                    <option value="Entrada" <?= $status == 'Entrada' ? 'selected' : '' ?>>Entrada</option>
                    <option value="Saída" <?= $status == 'Saída' ? 'selected' : '' ?>>Saída</option>
                </select>
                <input type="date" name="dataInicial" value="<?= htmlspecialchars($dataInicial) ?>" />
                <input type="date" name="dataFinal" value="<?= htmlspecialchars($dataFinal) ?>" />
                <button type="submit" name="buttonPesquisar"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
    </header>

    <main class="container mt-4">
        <h2>Movimentações</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Movimento</th>
                    <th>Valor</th>
                    <th>Funcionário</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($row == 0) {
                    echo "<tr><td colspan='5' class='text-center'>Não existem dados cadastrados no banco</td></tr>";
                } else {
                    while ($res = mysqli_fetch_assoc($result)) {
                        $tipoMov = $res['tipo'];
                        $movimento = $res['movimento'];
                        $valor = number_format($res['valor'], 2, ',', '.');
                        $funcionario = $res['funcionario'];
                        $data = implode('/', array_reverse(explode('-', $res['data'])));

                        $cor = ($tipoMov == 'Entrada') ? 'blue' : 'red';

                        echo "<tr>";
                        echo "<td><font color='$cor'>$tipoMov</font></td>";
                        echo "<td>$movimento</td>";
                        echo "<td><font color='$cor'>R$ $valor</font></td>";
                        echo "<td>$funcionario</td>";
                        echo "<td>$data</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>

        <div class="row mt-4">
            <!-- Card Entradas -->
            <div class="col-md-4 mb-3">
                <div class="card border-primary shadow">
                    <div class="card-body text-primary text-center">
                        <i class="fas fa-arrow-down fa-2x mb-2"></i>
                        <h5 class="card-title">Total de Entradas</h5>
                        <p class="card-text display-6">R$ <?= number_format($totalEntradas, 2, ',', '.') ?></p>
                    </div>
                </div>
            </div>

            <!-- Card Saídas -->
            <div class="col-md-4 mb-3">
                <div class="card border-danger shadow">
                    <div class="card-body text-danger text-center">
                        <i class="fas fa-arrow-up fa-2x mb-2"></i>
                        <h5 class="card-title">Total de Saídas</h5>
                        <p class="card-text display-6">R$ <?= number_format($totalSaidas, 2, ',', '.') ?></p>
                    </div>
                </div>
            </div>

            <!-- Card Saldo -->
            <div class="col-md-4 mb-3">
                <div class="card border-success shadow">
                    <div class="card-body text-success text-center">
                        <i class="fas fa-wallet fa-2x mb-2"></i>
                        <h5 class="card-title">Saldo</h5>
                        <p class="card-text display-6">R$ <?= number_format($saldo, 2, ',', '.') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>