<?php
$dataInicial = $_GET['dataInicial'];
$dataFinal = $_GET['dataFinal'];
$status = $_GET['status'];

$dataIni = implode('/', array_reverse(explode('-', $dataInicial)));
$dataFin = implode('/', array_reverse(explode('-', $dataFinal)));

include('../conexao.php');

?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<style>
    @page {
        margin: 0px;

    }

    .footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        background-color: #ebebeb;
        padding: 10px;
    }

    .cabecalho {
        background-color: #ebebeb;
        padding-top: 15px;
        margin-bottom: 30px;
    }

    .titulo {
        margin: 0;
    }

    .areaTotais {
        border: 0.5px solid #bcbcbc;
        padding: 15px;
        border-radius: 5px;
        margin-right: 25px;
    }

    .areaTotal {
        border: 0.5px solid #bcbcbc;
        padding: 15px;
        border-radius: 5px;
        margin-right: 25px;
        background-color: #f9f9f9;
        margin-top: 2px;
    }

    .pgto {
        margin: 1px;
    }
</style>


<div class="cabecalho">

    <div class="row">
        <div class="col-xs-12 text-center">
            <h2 class="titulo"><b>EletroService – Assistência Técnica para Eletrodomésticos</b></h2>
            <p>Rua dos Trabalhos Nº 1000, Centro - Irai de Minas - MG - CEP 38510000</p>
        </div>
    </div>


</div>

<div class="container">


    <div class="row">
        <div class="col-sm-6">
            <big><big> RELATÓRIO DE ORÇAMENTOS </big> </big>
        </div>
        <div class="col-sm-6">
            <small><?php
                    if ($status == 'Todos') {
                        echo 'Todos os Orçamentos';
                    } else {
                        echo 'Orçamentos com Status de: ' . $status;
                    }
                    ?></small>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-6">

        </div>
        <div class="col-sm-6">
            <small><b> Data Inicial:</b> <?php echo $dataIni; ?> <b> Data Final:</b> <?php echo $dataFin; ?> </small>
        </div>

    </div>

    <hr>

    <br><br>

    <?php

    $total_orc = 0;
    $quant = 0;
    $quant_aguardando = 0;
    $quant_abertos = 0;
    $quant_cancelados = 0;
    $quant_aprovados = 0;

    if ($status != 'Todos') {

        $query = "select o.tecnico, o.valor_total, o.data_abertura, o.data_geracao, o.data_aprovacao, o.status, f.nome as func_nome from orcamentos as o INNER JOIN funcionarios as f on o.tecnico = f.id where (o.data_abertura >= '$dataInicial' and o.data_abertura <= '$dataFinal') and o.status = '$status' order by o.data_abertura asc";
    } else {
        $query = "select o.tecnico, o.valor_total, o.data_abertura, o.data_geracao, o.data_aprovacao, o.status, f.nome as func_nome from orcamentos as o INNER JOIN funcionarios as f on o.tecnico = f.id where o.data_abertura >= '$dataInicial' and o.data_abertura <= '$dataFinal'  order by data_abertura asc";
    }



    $result = mysqli_query($conexao, $query);
    //$dado = mysqli_fetch_array($result);
    $row = mysqli_num_rows($result);

    if ($row == '') {

        echo "<h3> Não existem dados cadastrados no banco </h3>";
    } else {

    ?>


        <table class="table">
            <tr bgcolor="#f9f9f9">
                <td style="font-size:12px"> <b>Técnico</b> </td>
                <td style="font-size:12px"> <b>Valor Total</b> </td>
                <td style="font-size:12px"> <b> Data Abertura</b> </td>
                <td style="font-size:12px"> <b> Data Geração</b> </td>
                <td style="font-size:12px"> <b> Data Aprovação</b> </td>
                <td style="font-size:12px"> <b> Status</b> </td>

            </tr>


            <?php


            while ($res_1 = mysqli_fetch_array($result)) {
                $tecnico = $res_1["func_nome"];
                $total = $res_1["valor_total"];
                $data_abertura = $res_1["data_abertura"];
                $data_geracao = $res_1["data_geracao"];
                $data_aprovacao = $res_1["data_aprovacao"];
                $status_orc = $res_1["status"];

                $data_ab = implode('/', array_reverse(explode('-', $data_abertura)));
                $data_ger = implode('/', array_reverse(explode('-', $data_geracao)));
                $data_apr = implode('/', array_reverse(explode('-', $data_aprovacao)));


                $total_orc = $total_orc + $total;
                $quant = $quant + 1;

                if ($status_orc == 'Aberto') {
                    $quant_abertos = $quant_abertos + 1;
                }

                if ($status_orc == 'Aguardando') {
                    $quant_aguardando = $quant_aguardando + 1;
                }

                if ($status_orc == 'Cancelado') {
                    $quant_cancelados = $quant_cancelados + 1;
                }

                if ($status_orc == 'Aprovado') {
                    $quant_aprovados = $quant_aprovados + 1;
                }


            ?>

                <tr>
                    <td style="font-size:12px"> <?php echo $tecnico; ?> </td>
                    <td style="font-size:12px"> R$<?php echo $total; ?> </td>
                    <td style="font-size:12px"> <?php echo $data_ab; ?> </td>
                    <td style="font-size:12px"> <?php echo $data_ger; ?> </td>
                    <td style="font-size:12px"> <?php echo $data_apr; ?> </td>
                    <td style="font-size:12px"> <?php echo $status_orc; ?> </td>

                </tr>

            <?php } ?>
        </table>

    <?php } ?>


    <hr>


    <hr>

    <?php
    if ($status == 'Todos') {

    ?>

        <div class="row">
            <div class="col-md-12">
                <p style="font-size:12px">
                    <b>Orçamentos Aguardando:</b> <?php echo $quant_aguardando; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b>Orçamentos Abertos:</b> <?php echo $quant_abertos; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b>Orçamentos Aprovados:</b> <?php echo $quant_aprovados; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b>Orçamentos Cancelados:</b> <?php echo $quant_cancelados; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;


                </p>
            </div>

        </div>

    <?php } else {

    ?>

        <div class="row">
            <div class="col-sm-8">
                <small><b> Quantidade de Orçamentos:</b> <?php echo $quant; ?> </small>
            </div>
            <div class="col-sm-4">
                <small><b> Valor Total:</b> R$<?php echo $total_orc; ?>,00 </small>
            </div>

        </div>

    <?php
    }

    ?>

</div>


<div class="footer">
    <p style="font-size:12px" align="center">EletroService – Assistência Técnica para Eletrodomésticos</p>
</div>