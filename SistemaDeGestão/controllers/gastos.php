<?php
session_start();
include('../config/conexao.php');

// CADASTRAR Gasto
if (isset($_POST['button'])) {
    $valor = $_POST['txtvalor'];
    $motivo = $_POST['txtmotivo'];
    $funcionario = $_SESSION['nome_usuario']; // PEGA O NOME DO USUÁRIO LOGADO
    $insere = mysqli_query($conexao, "INSERT INTO gastos (valor, motivo, funcionario, data) VALUES ('$valor', '$motivo', '$funcionario', curDate())");


    //Recuperando o ultimo id Lançado
    $insere_id = mysqli_query($conexao, "select * from gastos order by id desc limit 1");
    while($res_id = mysqli_fetch_array($insere_id)){
        $id_gasto = $res_id['id'];
    }

    if ($insere) {
        echo "<script>alert('Salvo com Sucesso!'); window.location.href = 'gastos.php';</script>";
        //Insere na tabela movimentações
        $insere_mov = mysqli_query($conexao, "INSERT INTO movimentacoes (tipo, movimento, valor, funcionario, data, id_movimento) VALUES ('Saida', 'Gasto', '$valor', '$funcionario', curDate(), '$id_gasto')");
    } else {
        echo "<script>alert('Erro ao salvar');</script>";
    }
}

// EXCLUIR Gasto
if (isset($_GET['func']) && $_GET['func'] == 'deleta') {
    $id = $_GET['id'];
    mysqli_query($conexao, "DELETE FROM gastos WHERE id = '$id'");

    //Deleta tambem na tabela de movimentações
    mysqli_query($conexao, "DELETE FROM movimentacoes WHERE movimento = 'Gasto' and id_movimento = '$id'");


    echo "<script>alert('Gasto Excluído'); window.location.href = 'gastos.php';</script>";
}

// EDITAR Gasto
if (isset($_POST['button_editar'])) {
    $id = $_POST['id_edit']; // Corrigido: pegar o id do hidden input
    $motivo = $_POST['txtmotivo_edit'];
    $editar = mysqli_query($conexao, "UPDATE gastos SET motivo='$motivo' WHERE id='$id'");
    if ($editar) {
        echo "<script>alert('Gasto atualizado com sucesso!'); window.location.href = 'gastos.php';</script>";
    } else {
        echo "<script>alert('Erro ao editar Gasto!');</script>";
    }
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
    <title>Gastos</title>
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
        <h2>Tabela de Gastos</h2>
        <a class="btn" href="#" onclick="document.getElementById('modalCadastrar').style.display='flex'">+ Novo Gasto</a>
        <table>
            <thead>
                <tr>
                    <th>Valor</th>
                    <th>Motivo</th>
                    <th>Funcionário</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Busca os gastos do banco
                if (!empty($_GET['txtpesquisar'])) {
                    $data = $_GET['txtpesquisar'];
                    $query = "SELECT * FROM gastos WHERE data = '$data' ORDER BY id ASC";
                } else {
                    $query = "SELECT * FROM gastos WHERE data = curDate() ORDER BY id ASC";
                }
                $result = mysqli_query($conexao, $query);
                while ($row = mysqli_fetch_array($result)) {
                    $data2 = implode('/', array_reverse(explode('-', $row['data'])));
                    echo "<tr>
                            <td>{$row['valor']}</td>
                            <td>{$row['motivo']}</td>
                            <td>{$row['funcionario']}</td>
                            <td>{$data2}</td>
                            <td class='actions'>
                                <a href='gastos.php?func=edita&id={$row['id']}'><i class='fa-solid fa-pen-to-square'></i></a>
                                <a href='?func=deleta&id={$row['id']}'><i class='fa-solid fa-trash'></i></a>
                            </td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </main>

    <?php
    if (isset($_GET['func']) && $_GET['func'] == 'edita') {
        $id = $_GET['id'];
        $result = mysqli_query($conexao, "SELECT * FROM gastos WHERE id = '$id'");
        $res_1 = mysqli_fetch_array($result);
    ?>
        <div id="modalEditar" class="modal" style="display:flex;">
            <div class="modal-content">
                <h3>Editar Gasto</h3>
                <form method="POST">
                    <input type="hidden" name="id_edit" value="<?php echo $res_1['id']; ?>">
                    <label for="txtmotivo_edit">Motivo</label>
                    <input type="text" name="txtmotivo_edit" placeholder="Motivo"  value="<?php echo $res_1['motivo']; ?>" required>
                    <div class="botoes">
                        <button type="submit" name="button_editar">Atualizar</button>
                        <button type="button" onclick="document.getElementById('modalEditar').style.display='none'">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    <?php } ?>

    <!-- Modal Cadastro -->
    <div id="modalCadastrar" class="modal">
        <div class="modal-content">
            <h3>Novo Gasto</h3>
            <form method="POST">
                <label for="txtvalor">Valor</label>
                <input type="text" name="txtvalor" placeholder="Valor" required>
                <label for="txtmotivo">Motivo</label>
                <input type="text" name="txtmotivo" placeholder="Motivo" required>
                <div class="botoes">
                    <button type="submit" name="button">Salvar</button>
                    <button type="button" onclick="document.getElementById('modalCadastrar').style.display='none'">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>