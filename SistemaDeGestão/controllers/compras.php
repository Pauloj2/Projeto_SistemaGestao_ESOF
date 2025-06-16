<?php
session_start();
include('../config/conexao.php');

// CADASTRAR Pagamento
if (isset($_POST['button'])) {
    $valor = $_POST['txtvalor'];
    $peca = $_POST['txtpeca'];
    $funcionario = $_SESSION['nome_usuario'];

    $insere = mysqli_query($conexao, "INSERT INTO compras (valor, peca, funcionario, data) VALUES ('$valor', '$peca', '$funcionario', curDate())");


    //Recuperando o ultimo id Lançado
    $insere_id = mysqli_query($conexao, "select * from compras order by id desc limit 1");
    while ($res_id = mysqli_fetch_array($insere_id)) {
        $id_ultimo = $res_id['id'];
    }

    if ($insere) {
        echo "<script>alert('Salvo com Sucesso!'); window.location.href = 'compras.php';</script>";

        //Insere na tabela movimentações
        $insere_mov = mysqli_query($conexao, "INSERT INTO movimentacoes (tipo, movimento, valor, funcionario, data, id_movimento) VALUES ('Saida', 'Compra', '$valor', '$funcionario', curDate(), '$id_ultimo')");

    } else {
        echo "<script>alert('Erro ao salvar');</script>";
    }
}

// EXCLUIR Vendas
if (isset($_GET['func']) && $_GET['func'] == 'deleta') {
    $id = $_GET['id'];
    
    $funcionario = $_SESSION['nome_usuario'];

    $query = "DELETE FROM compras where id = '$id'";

    mysqli_query($conexao, $query);

    $query_mov = "DELETE FROM movimentacoes where movimento = 'Compra' and id_movimento = '$id'";
    mysqli_query($conexao, $query_mov);

    echo "<script language='javascript'> window.location='compras.php'; </script>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/cssClientes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Compras</title>
</head>

<body>
    <header>
        <div class="voltar">
            <a href="../views/painel_tesouraria.php">
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
        <h2>Tabela de Compras</h2>
        <a class="btn" href="#" onclick="document.getElementById('modalCadastrar').style.display='flex'">+ Nova Compra</a>
        <table>
            <thead>
                <tr>
                    <th>Valor</th>
                    <th>Peça</th>
                    <th>Funcionário</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Busca os vendas do banco
                if (!empty($_GET['txtpesquisar'])) {
                    $data = $_GET['txtpesquisar'];
                    $query = "SELECT * FROM compras WHERE data = '$data' ORDER BY id ASC";
                } else {
                    $query = "SELECT * FROM compras WHERE data = curDate() ORDER BY id ASC";
                }
                $result = mysqli_query($conexao, $query);
                while ($row = mysqli_fetch_array($result)) {
                    $data2 = implode('/', array_reverse(explode('-', $row['data'])));
                    echo "<tr>
                        <td>{$row['valor']}</td>
                        <td>{$row['peca']}</td>
                        <td>{$row['funcionario']}</td>
                        <td>{$data2}</td>
                        <td class='actions'>
                            <a href='?func=deleta&id={$row['id']}'><i class='fa-solid fa-trash'></i></a>
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
            <h3>Nova Compra</h3>
            <form method="POST">
                <label for="txtvalor">Valor</label>
                <br>
                <input type="text" name="txtvalor" placeholder="Valor" required>

                <label for="txtvalor">Peça</label>
                <br>
                <input type="text" name="txtpeca" placeholder="Peça" required>
                
                <div class="botoes">
                    <button type="submit" name="button">Salvar</button>
                    <button type="button" onclick="document.getElementById('modalCadastrar').style.display='none'">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>