<?php
$dataInicial = $_GET['dataInicial'] ?? date('Y-m-d');
$dataFinal = $_GET['dataFinal'] ?? date('Y-m-d');

$dataIni = implode('/', array_reverse(explode('-', $dataInicial)));
$dataFin = implode('/', array_reverse(explode('-', $dataFinal)));

include('../config/conexao.php');
?>

<style>
    @page {
        margin: 0;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        padding: 20px;
    }

    .cabecalho {
        background-color: #ebebeb;
        padding: 20px;
        margin-bottom: 30px;
        text-align: center;
    }

    .titulo {
        margin: 0;
        font-size: 18px;
        font-weight: bold;
    }

    .subtitulo {
        font-size: 12px;
        margin-top: 5px;
    }

    .resumo {
        margin-top: 10px;
        text-align: right;
    }

    .tabela {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .tabela th, .tabela td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
    }

    .tabela th {
        background-color: #f9f9f9;
    }

    .rodape {
        position: absolute;
        bottom: 10px;
        width: 100%;
        text-align: center;
        font-size: 10px;
        color: #777;
    }
</style>

<div class="cabecalho">
    <h2 class="titulo">EletroService – Assistência Técnica para Eletrodomésticos</h2>
    <p class="subtitulo">Rua dos Trabalhos Nº 1000, Centro - Iraí de Minas - MG - CEP 38510000</p>
</div>

<h3>Relatório de Pagamentos</h3>
<p><strong>Período:</strong> <?= $dataIni ?> a <?= $dataFin ?></p>

<?php
$total_gastos = 0;
$quant = 0;

$query = "SELECT * FROM pagamentos WHERE data BETWEEN '$dataInicial' AND '$dataFinal' ORDER BY data ASC";
$result = mysqli_query($conexao, $query);
$row = mysqli_num_rows($result);
?>

<?php if ($row == 0): ?>
    <p><strong>Não existem dados cadastrados no banco.</strong></p>
<?php else: ?>
    <table class="tabela">
        <thead>
            <tr>
                <th>Funcionário Pago</th>
                <th>Valor</th>
                <th>Tesoureiro</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($res = mysqli_fetch_array($result)):
                $funcionario = $res["funcionario"];
                $valor = $res["valor"];
                $tesoureiro = $res["tesoureiro"];
                $data = implode('/', array_reverse(explode('-', $res["data"])));

                $total_gastos += $valor;
                $quant++;
            ?>
                <tr>
                    <td><?= $funcionario ?></td>
                    <td>R$ <?= number_format($valor, 2, ',', '.') ?></td>
                    <td><?= $tesoureiro ?></td>
                    <td><?= $data ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <br>
    <div class="resumo">
        <p><strong>Quantidade de Compras:</strong> <?= $quant ?></p>
        <p><strong>Valor Total:</strong> R$ <?= number_format($total_gastos, 2, ',', '.') ?></p>
    </div>
<?php endif; ?>

<div class="rodape">
    <p>EletroService – Assistência Técnica para Eletrodomésticos</p>
</div>
