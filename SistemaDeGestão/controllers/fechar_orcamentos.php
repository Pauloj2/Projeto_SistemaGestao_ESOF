<?php
session_start();
include('../config/conexao.php');
include('verificar_login.php');

// FECHAR Orçamento
if (isset($_POST['buttonFechar'])) {
    $id = $_POST['id_orcamento'];
    $valor_servico = $_POST['txtvalor'];
    $pecas = $_POST['txtpecas'];
    $valor_pecas = $_POST['txtvalorPecas'];
    $laudo = $_POST['txtlaudo'];
    $desconto = 0;
    $valor_total = $valor_servico + $valor_pecas;
    $status = 'Aguardando';

    $query_fechar = "UPDATE orcamentos SET laudo = '$laudo', valor_servico = '$valor_servico', 
                    pecas = '$pecas', valor_pecas = '$valor_pecas', desconto = '$desconto', 
                    total = '$valor_total', valor_total = '$valor_total', 
                    data_geracao = curDate(), status = '$status' 
                    WHERE id = '$id'";

    $result_fechar = mysqli_query($conexao, $query_fechar);

    if (!$result_fechar) {
        echo "<script>alert('Ocorreu um erro ao Fechar o Orçamento!');</script>";
    } else {
        echo "<script>alert('Orçamento Fechado com Sucesso!'); window.location.href = 'fechar_orcamentos.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fechar Orçamentos</title>
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
        <h2>Orçamentos Abertos para Fechamento</h2>

        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Técnico</th>
                    <th>Produto</th>
                    <th>Defeito</th>
                    <th>Data Abertura</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // LISTAR ORÇAMENTOS ABERTOS
                $usuario = $_SESSION['nome_usuario'];

                if (isset($_GET['buttonPesquisar']) && $_GET['txtpesquisar'] != '') {
                    $data = $_GET['txtpesquisar'];
                    $query = "SELECT o.id, o.cliente, o.tecnico, o.produto, o.problema, o.status, o.data_abertura, 
                             c.nome as cli_nome, f.nome as func_nome 
                             FROM orcamentos as o 
                             INNER JOIN clientes as c ON o.cliente = c.cpf 
                             INNER JOIN funcionarios as f ON o.tecnico = f.id 
                             WHERE data_abertura = '$data' AND f.nome = '$usuario' AND o.status = 'Aberto' 
                             ORDER BY o.id ASC";
                } else {
                    $query = "SELECT o.id, o.cliente, o.tecnico, o.produto, o.problema, o.status, o.data_abertura, 
                             c.nome as cli_nome, f.nome as func_nome 
                             FROM orcamentos as o 
                             INNER JOIN clientes as c ON o.cliente = c.cpf 
                             INNER JOIN funcionarios as f ON o.tecnico = f.id 
                             WHERE f.nome = '$usuario' AND o.status = 'Aberto' 
                             ORDER BY o.id ASC";
                }

                $result = mysqli_query($conexao, $query);
                $row = mysqli_num_rows($result);

                if ($row <= 0) {
                    echo "<tr><td colspan='6'><h3>Não existem orçamentos abertos para fechamento</h3></td></tr>";
                } else {
                    while ($res_1 = mysqli_fetch_array($result)) {
                        $cliente = $res_1["cli_nome"];
                        $tecnico = $res_1["func_nome"];
                        $produto = $res_1["produto"];
                        $defeito = $res_1["problema"];
                        $data_abertura = $res_1["data_abertura"];
                        $id = $res_1["id"];

                        // Formatando a data para o padrão brasileiro
                        $data_formatada = implode('/', array_reverse(explode('-', $data_abertura)));

                        echo "<tr>
                            <td>$cliente</td>
                            <td>$tecnico</td>
                            <td>$produto</td>
                            <td>$defeito</td>
                            <td>$data_formatada</td>
                            <td class='actions' style='text-align: center;'>
                                <a href='fechar_orcamentos.php?func=fechar&id=$id'>
                                    <i class='fa-solid fa-pen-to-square'></i>
                                </a>
                            </td>
                            </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </main>

    <?php
    // Modal de Fechamento de Orçamento
    if (isset($_GET['func']) && $_GET['func'] == 'fechar') {
        $id = $_GET['id'];

        // Buscar informações do orçamento
        $query = "SELECT o.*, c.nome as cli_nome FROM orcamentos as o 
                 INNER JOIN clientes as c ON o.cliente = c.cpf 
                 WHERE o.id = '$id'";
        $result = mysqli_query($conexao, $query);
        $res_1 = mysqli_fetch_array($result);
        $cliente = $res_1["cli_nome"];
        $produto = $res_1["produto"];
    ?>
        <div id="modalFechar" class="modal" style="display:flex;">
            <div class="modal-content">
                <h3>Fechar Orçamento</h3>
                <p><strong>Cliente:</strong> <?php echo $cliente; ?></p>
                <p><strong>Produto:</strong> <?php echo $produto; ?></p>

                <form method="POST">
                    <input type="hidden" name="id_orcamento" value="<?php echo $id; ?>">

                    <div class="form-group">
                        <label for="txtvalor">Valor Serviço</label>
                        <input type="text" id="txtvalor" name="txtvalor" placeholder="Valor Mão de Obra" required>
                    </div>

                    <div class="form-group">
                        <label for="txtpecas">Peças</label>
                        <input type="text" id="txtpecas" name="txtpecas" placeholder="Peças" required>
                    </div>

                    <div class="form-group">
                        <label for="txtvalorPecas">Valor Peças</label>
                        <input type="text" id="txtvalorPecas" name="txtvalorPecas" placeholder="Valor das Peças" required>
                    </div>

                    <div class="form-group">
                        <label for="txtlaudo">Laudo</label>
                        <textarea id="txtlaudo" name="txtlaudo" placeholder="Laudo Técnico" required></textarea>
                    </div>

                    <div class="botoes">
                        <button type="submit" name="buttonFechar">Fechar Orçamento</button>
                        <button type="button" onclick="window.location.href='fechar_orcamentos.php'">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.getElementById('modalFechar').style.display = 'flex';
        </script>
    <?php } ?>

    <script>
        $(document).ready(function() {
            $('#txtvalor').mask('###0.00', {
                reverse: true
            });
            $('#txtvalorPecas').mask('###0.00', {
                reverse: true
            });
        });
    </script>
</body>

</html>