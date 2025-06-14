<?php
session_start();
include('conexao.php');
include('verificar_login.php');
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços do Cliente</title>
    <link rel="stylesheet" href="cssClientes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <style>
        *{
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

    </style>
</head>

<body>
    <header>
        <div class="voltar">
            <a href="painel_funcionario.php"><i class="fa-solid fa-arrow-left"></i></a>
        </div>
    </header>

    <main class="container">
        <h2 style="color: blue;">Orçamentos do Cliente</h2>

        <table>
            <thead>
                <tr style="color: blue;">
                    <th>Produto</th>
                    <th>Problema</th>
                    <th>Valor</th>
                    <th>Data Abertura</th>
                    <th>Data Aprovação</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // LISTAR ORÇAMENTOS ABERTOS
                $usuario = $_SESSION['nome_usuario'];
                $cpf = $_GET['cpf'];

                $query = "SELECT * from orcamentos where cliente = '$cpf' ";

                $result = mysqli_query($conexao, $query);
                $row = mysqli_num_rows($result);

                if ($row <= 0) {
                    echo "<tr><td colspan='6'><h3>Não existem orçamentos abertos para fechamento</h3></td></tr>";
                } else {
                    while ($res_1 = mysqli_fetch_array($result)) {
                        $produto = $res_1["produto"];
                        $problema = $res_1["problema"];
                        $valor_total = $res_1["valor_total"];
                        $data_abertura = $res_1["data_abertura"];
                        $data_aprovacao = $res_1["data_aprovacao"];
                        $status = $res_1["status"];
                        $id = $res_1["id"];

                        // Formatando a data para o padrão brasileiro
                        $data2 = implode('/', array_reverse(explode('-', $data_abertura)));
                        $data3 = implode('/', array_reverse(explode('-', $data_aprovacao)));

                        echo "<tr>
                            <td>$produto</td>
                            <td>$problema</td>
                            <td>$valor_total</td>
                            <td>$data2</td>
                            <td>$data3</td>
                            <td>$status</td>
                            </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </main>

    <main class="container">
        <h2 style="color:green">Ordens de Serviço do Cliente</h2>

        <table>
            <thead>
                <tr style="color: green;">
                    <th>Produto</th>
                    <th>Total</th>
                    <th>Data Abertura</th>
                    <th>Data Fechamneto</th>
                    <th>Garantia</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // LISTAR ORÇAMENTOS ABERTOS
                $usuario = $_SESSION['nome_usuario'];
                $cpf = $_GET['cpf'];

                $query = "SELECT * from os where cliente = '$cpf' ";

                $result = mysqli_query($conexao, $query);
                $row = mysqli_num_rows($result);

                if ($row <= 0) {
                    echo "<tr><td colspan='6'><h3>Não existem orçamentos abertos para fechamento</h3></td></tr>";
                } else {
                    while ($res_1 = mysqli_fetch_array($result)) {
                        $produto = $res_1["produto"];
                        $total = $res_1["total"];
                        $data_abertura = $res_1["data_abertura"];
                        $data_fechamento = $res_1["data_fechamento"];
                        $garantia = $res_1["garantia"];
                        $status = $res_1["status"];
                        $id = $res_1["id"];

                        // Formatando a data para o padrão brasileiro
                        $data2 = implode('/', array_reverse(explode('-', $data_abertura)));
                        $data3 = implode('/', array_reverse(explode('-', $data_fechamento)));

                        echo "<tr>
                            <td>$produto</td>
                            <td>$total</td>
                            <td>$data2</td>
                            <td>$data3</td>
                            <td>$garantia</td>
                            <td>$status</td>
                            </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </main>


</body>

</html>