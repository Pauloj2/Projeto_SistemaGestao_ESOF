<?php
include('../config/conexao.php');
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar OS</title>
    <link rel="stylesheet" href="../assets/cssClientes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
</head>

<body>

    <header>
        <div class="voltar">
            <a href="../views/painel_funcionario.php"><i class="fa-solid fa-arrow-left"></i></a>
        </div>
        <div class="pesquisar">
            <form class="campo-pesquisa" method="GET">
                <select name="status">
                    <option value="Todos">Todos</option>
                    <option value="Aberta">Aberta</option>
                    <option value="Fechada">Fechada</option>
                    <option value="Cancelada">Cancelada</option>
                </select>
                <input type="date" name="txtpesquisar" placeholder="Pesquisar por data">
                <button type="submit" name="buttonPesquisar"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
    </header>

    <main class="container">
        <h2>Ordens de Serviço</h2>
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Produto</th>
                    <th>Técnico</th>
                    <th>Valor Total</th>
                    <th>Data Abertura</th>
                    <th>Data Fechamento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // LISTAR TODOS OS ORÇAMENTOS

                // Consulta otimizada com JOIN
                $query = "
                    SELECT 
                        os.*,
                        c.nome AS nome_cliente,
                        c.email,
                        f.nome AS nome_tecnico
                    FROM os
                    LEFT JOIN clientes c ON c.cpf = os.cliente
                    LEFT JOIN funcionarios f ON f.id = os.tecnico
                    WHERE 1=1  -- Condição inicial para facilitar a concatenação
                ";

                // Adiciona condições de filtro
                if (isset($_GET['buttonPesquisar'])) {
                    if (!empty($_GET['txtpesquisar'])) {
                        $data = mysqli_real_escape_string($conexao, $_GET['txtpesquisar']);
                        $query .= " AND os.data_abertura = '$data'";
                    }
                    if ($_GET['status'] != 'Todos') {
                        $statusOrc = mysqli_real_escape_string($conexao, $_GET['status']);
                        $query .= " AND os.status = '$statusOrc'";
                    }
                } else {
                    $query .= " AND os.data_abertura = curDate()";
                }

                $query .= " ORDER BY os.id ASC";

                $result = mysqli_query($conexao, $query);
                $row = mysqli_num_rows($result);

                if ($row == 0) {
                    echo "<tr><td colspan='8'><h3>Não existem dados cadastrados no banco</h3></td></tr>";
                } else {
                    while ($res_1 = mysqli_fetch_array($result)) {
                        $cliente = htmlspecialchars($res_1["nome_cliente"], ENT_QUOTES, 'UTF-8'); // Use o nome do cliente do JOIN
                        $tecnico = htmlspecialchars($res_1["nome_tecnico"], ENT_QUOTES, 'UTF-8'); // Use o nome do técnico do JOIN
                        $produto = htmlspecialchars($res_1["produto"], ENT_QUOTES, 'UTF-8');
                        $valor_total = htmlspecialchars($res_1["total"], ENT_QUOTES, 'UTF-8');
                        $status = htmlspecialchars($res_1["status"], ENT_QUOTES, 'UTF-8');
                        $data_abertura = htmlspecialchars($res_1["data_abertura"], ENT_QUOTES, 'UTF-8');
                        $data_fechamento = htmlspecialchars($res_1["data_fechamento"], ENT_QUOTES, 'UTF-8');
                        $id = htmlspecialchars($res_1["id"], ENT_QUOTES, 'UTF-8');
                        $id_orc = htmlspecialchars($res_1["id_orc"], ENT_QUOTES, 'UTF-8');
                        $email = htmlspecialchars($res_1["email"], ENT_QUOTES, 'UTF-8'); // Pega o email do JOIN

                        $data2 = implode('/', array_reverse(explode('-', $data_abertura)));
                        $data3 = implode('/', array_reverse(explode('-', $data_fechamento)));

                        echo "<tr>";

                        

                        if ($status == "Fechada") {
                            echo "<td><a class='link' href='rel/rel_os_class.php?id=$id&id_orc=$id_orc&email=$email' target='_blank'>$cliente</a></td>";
                        } else {
                            echo "<td>$cliente</td>";
                        }

                        echo "<td>$produto</td>
                            <td>$tecnico</td>
                            <td>$valor_total</td>
                            <td>$data2</td>
                            <td>$data3</td>
                            <td>$status</td>
                            <td class='actions' style='text-align: center;'>
                                <a href='rel/rel_os_class.php?id=$id&id_orc=$id_orc&email=$email' target='_blank'><i class='fa fa-file-text' style='color: green;'></i>
                
                            </td>
                            </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </main>
    <?php
    if (isset($_GET['func']) && $_GET['func'] == 'deleta') {
        $id = $_GET['id'];
        $query = "DELETE FROM os WHERE id = '$id'";
        mysqli_query($conexao, $query);
        echo "<script>window.location='consultar_os.php';</script>";
    }
    ?>

</body>

</html>