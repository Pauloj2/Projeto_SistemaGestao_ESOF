<?php
session_start();
include('conexao.php');

// CADASTRAR Pagamento
if (isset($_POST['button'])) {
    $valor = $_POST['txtvalor'];
    $produto = $_POST['txtproduto'];
    $status = $_POST['txtstatus'];
    $funcionario = $_SESSION['nome_usuario'];

    $insere = mysqli_query($conexao, "INSERT INTO vendas (valor, produto, funcionario, data, status) VALUES ('$valor', '$produto', '$funcionario', curDate(), 'Efetuada')");


    //Recuperando o ultimo id Lançado
    $insere_id = mysqli_query($conexao, "select * from vendas order by id desc limit 1");
    while ($res_id = mysqli_fetch_array($insere_id)) {
        $id_ultimo = $res_id['id'];
    }

    if ($insere) {
        echo "<script>alert('Salvo com Sucesso!'); window.location.href = 'vendas.php';</script>";
        //Insere na tabela movimentações
        $insere_mov = mysqli_query($conexao, "INSERT INTO movimentacoes (tipo, movimento, valor, funcionario, data, id_movimento) VALUES ('Entrada', 'Venda', '$valor', '$funcionario', curDate(), '$id_ultimo')");

        $query_pro = mysqli_query($conexao, "DELETE From produtos where produto = '$produto' ");
    } else {
        echo "<script>alert('Erro ao salvar');</script>";
    }
}

// EXCLUIR Vendas
if (isset($_GET['func']) && $_GET['func'] == 'deleta') {
    $id = $_GET['id'];
    $produto = $_GET['produto'];
    $funcionario = $_SESSION['nome_usuario'];

    $query = "UPDATE vendas set funcionario = '$funcionario', status = 'Cancelada' where id = '$id'";
    mysqli_query($conexao, $query);

    $query_mov = "DELETE FROM movimentacoes where movimento = 'Venda' and id_movimento = '$id'";
    mysqli_query($conexao, $query_mov);


    //DEVOLVER O PRODUTO PARA AS VENDAS - NA TABELA PRODUTOS
    $query_produto = "INSERT INTO produtos (produto) values ('$produto') ";

    mysqli_query($conexao, $query_produto);


    echo "<script language='javascript'> window.location='vendas.php'; </script>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cssClientes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Vendas</title>
</head>

<body>
    <header>
        <div class="voltar">
            <a href="painel_tesouraria.php">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
        <div class="pesquisar">
            <form class="campo-pesquisa" method="GET">
                <input type="date" name="txtpesquisar">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
    </header>

    <main class="container">
        <h2>Tabela de Vendas</h2>
        <a class="btn" href="#" onclick="document.getElementById('modalCadastrar').style.display='flex'">+ Nova Venda</a>
        <table>
            <thead>
                <tr>
                    <th>Valor</th>
                    <th>Produto</th>
                    <th>Funcionário</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Busca os vendas do banco
                if (!empty($_GET['txtpesquisar'])) {
                    $data = $_GET['txtpesquisar'];
                    $query = "SELECT * FROM vendas WHERE data = '$data' ORDER BY id ASC";
                } else {
                    $query = "SELECT * FROM vendas WHERE data = curDate() ORDER BY id ASC";
                }
                $result = mysqli_query($conexao, $query);
                while ($row = mysqli_fetch_array($result)) {
                    $data2 = implode('/', array_reverse(explode('-', $row['data'])));
                    echo "<tr>
                        <td>{$row['valor']}</td>
                        <td>{$row['produto']}</td>
                        <td>{$row['funcionario']}</td>
                        <td>{$data2}</td>
                        <td>{$row['status']}</td>
                        <td class='actions'>
                            <a href='?func=deleta&id={$row['id']}&produto={$row['produto']}'><i class='fa-solid fa-trash'></i></a>
                        </td>
                    </tr>";
                    }
                ?>
            </tbody>
        </table>
    </main>


    <!-- Modal Cadastro -->
    <div id="modalCadastrar" class="modal">
        <div class="modal-content">
            <h3>Nova Venda</h3>
            <form method="POST">
                <label for="txtvalor">Valor</label>
                <br>
                <input type="text" name="txtvalor" placeholder="Valor" required>

                <label for="txtproduto">Produto</label>
                <br>
                <select name="txtproduto" required>
                    <option value="" disabled selected>Selecione o Produto</option>
                    <?php
                    $funcs = mysqli_query($conexao, "SELECT * from produtos ORDER BY produto ASC");
                    while ($f = mysqli_fetch_array($funcs)) {
                        echo "<option value='{$f['produto']}'>{$f['produto']}</option>";
                    }
                    ?>
                </select>
                <div class="botoes">
                    <button type="submit" name="button">Salvar</button>
                    <button type="button" onclick="document.getElementById('modalCadastrar').style.display='none'">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>