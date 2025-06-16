<?php
include('../config/conexao.php');
session_start();

$tecnico = $_SESSION['nome_usuario'];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordens de Serviço</title>
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
                <input type="date" name="txtpesquisar" placeholder="Pesquisar por data">
                <button type="submit" name="buttonPesquisar"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
    </header>

    <main class="container">
        <h2>Lista de Ordens de Serviço</h2>
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Produto</th>
                    <th>Valor Total</th>
                    <th>Data Abertura</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // --- LISTAR ORDENS DE SERVIÇO COM STATUS "Aprovado" ---
                if (isset($_GET['buttonPesquisar']) && $_GET['txtpesquisar'] != '') {
                    $data = $_GET['txtpesquisar'];
                    $query = "SELECT ord.id, ord.cliente, ord.produto, ord.tecnico, ord.total, ord.data_abertura, 
                    ord.data_fechamento, ord.status, fun.nome 
                    FROM os AS ord 
                    INNER JOIN funcionarios AS fun ON ord.tecnico = fun.id 
                    WHERE DATE(ord.data_abertura) = '$data' 
                    AND ord.status = 'Aberta' 
                    AND fun.nome = '$tecnico' 
                    ORDER BY ord.id ASC";
                } else {
                    $query = "SELECT ord.id, ord.cliente, ord.produto, ord.tecnico, ord.total, ord.data_abertura, 
                    ord.data_fechamento, ord.status, fun.nome 
                    FROM os AS ord 
                    INNER JOIN funcionarios AS fun ON ord.tecnico = fun.id 
                    WHERE ord.status = 'Aberta' 
                    AND fun.nome = '$tecnico' 
                    ORDER BY ord.id ASC";
                }


                $result = mysqli_query($conexao, $query);
                $row_count = mysqli_num_rows($result);

                if ($row_count <= 0) {
                    echo "<tr><td colspan='5'><h3>Não existem ordens de serviço para exibir.</h3></td></tr>";
                } else {
                    while ($res_1 = mysqli_fetch_array($result)) {
                        $cliente = $res_1["cliente"];
                        $produto = $res_1["produto"];
                        $valor_total = $res_1["total"];
                        $data_abertura = $res_1["data_abertura"];
                        $id = $res_1["id"];

                        echo "<tr>
                                <td>$cliente</td>
                                <td>$produto</td>
                                <td>R$ $valor_total</td>
                                <td>$data_abertura</td>
                                <td class='actions'>
                                    <a href='os_abertas.php?func=edita&id=$id&valor=$valor_total' title='Editar Orçamento'><i class='fa fa-edit'></i></a>

                                    <a href='os_abertas.php?func=deleta&id=$id&produto=$produto' title='Excluir Orçamento' onclick=\"return confirm('Tem certeza que deseja excluir esta Ordem de Serviço?');\"><i class='fa fa-trash'></i></a>
                                </td>
                            </tr>";
                        }
                }
                ?>
            </tbody>
        </table>
    </main>
    <?php
    if (@$_GET['func'] == 'deleta') {
        $id = $_GET['id'];
        $produto = $_GET['produto'];

        $query_editar = "UPDATE os set status = 'Cancelada' where id = '$id' ";

        mysqli_query($conexao, $query_editar);


        //INSERINDO O PRODUTO NA TABELA DE PRODUTOS
        $query_produto = "INSERT INTO produtos (produto) values ('$produto') ";

        mysqli_query($conexao, $query_produto);


        echo "<script language='javascript'> window.location='os_abertas.php'; </script>";
    }
    ?>

    <?php
    if (isset($_GET['func']) && $_GET['func'] == 'edita') {
        $id_editar = $_GET['id'];

        // Recupera a OS
        $query_os = "SELECT * FROM os WHERE id = '$id_editar'";
        $result_os = mysqli_query($conexao, $query_os);
        $res_os = mysqli_fetch_array($result_os);

        if ($res_os) {
            $id_orc = $res_os['id_orc'];

            // Recupera o orçamento associado
            $query_orc = "SELECT * FROM orcamentos WHERE id = '$id_orc'";
            $result_orc = mysqli_query($conexao, $query_orc);
            $res_orc = mysqli_fetch_array($result_orc);

            $cpf = isset($res_orc['cliente']) ? $res_orc['cliente'] : '';

            // Recupera o email do cliente
            $query_cli = "SELECT * FROM clientes WHERE cpf = '$cpf'";
            $result_cli = mysqli_query($conexao, $query_cli);
            $res_cli = mysqli_fetch_array($result_cli);

            $email = isset($res_cli['email']) ? $res_cli['email'] : '';
    ?>
            <div id="modalEditar" class="modal" style="display:flex;">
                <div class="modal-content">
                    <h4 class="modal-title">Fechar Ordem de Serviço</h4>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $id_editar ?>">
                        <input type="hidden" name="id_orc" value="<?= $id_orc ?>">
                        <input type="hidden" name="email" value="<?= $email ?>">

                        <div class="form-group">
                            <label for="txtgarantia">Garantia do Serviço</label>
                            <select id="txtgarantia" name="txtgarantia" required>
                                <option value="">Selecione</option>
                                <option value="30 dias">30 dias</option>
                                <option value="60 dias">60 dias</option>
                                <option value="90 dias">90 dias</option>
                                <option value="180 dias">180 dias</option>
                            </select>
                        </div>

                        <div class="botoes">
                            <button type="submit" name="buttonEditar">Salvar</button>
                            <button type="button" onclick="document.getElementById('modalEditar').style.display='none'; window.location.href = 'os_abertas.php';">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                document.getElementById('modalEditar').style.display = 'flex';
            </script>
    <?php
        }
    }
    ?>


    <script>
        $("#modalEditar").modal("show");
    </script>

    <!--Comando para editar os dados UPDATE -->
    <?php
    if (isset($_POST['buttonEditar'])) {
        $id = $_POST['id']; // ID da OS
        $id_orc = $_POST['id_orc']; // ID do orçamento
        $email = $_POST['email'];
        $garantia = $_POST['txtgarantia'];

        $query_editar = "UPDATE os SET garantia = '$garantia', data_fechamento = CURDATE(), status = 'Fechada' WHERE id = '$id'";
        $result_editar = mysqli_query($conexao, $query_editar);

        $funcionario = $_SESSION['nome_usuario'];
        $valor = $_GET['valor'];

        //Insere na tabela movimentações
        $insere_mov = mysqli_query($conexao, "INSERT INTO movimentacoes (tipo, movimento, valor, funcionario, data, id_movimento) VALUES ('Entrada', 'Serviço', '$valor', '$funcionario', curDate(), '$id')");

        if (!$result_editar) {
            echo "<script>alert('Erro ao editar OS.');</script>";
        } else {
            echo "<script>alert('OS fechada com sucesso!');</script>";
            echo "<script>window.location='rel/rel_os_class.php?id=$id&id_orc=$id_orc';</script>";
        }
    }
    ?>


    <script>
        $(document).ready(function() {
            $('#txtcpf').mask('000.000.000-00');
        });
    </script>

</body>

</html>