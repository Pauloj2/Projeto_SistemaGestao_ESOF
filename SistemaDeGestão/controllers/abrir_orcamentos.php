<?php
include('../config/conexao.php');

session_start();
include('verificar_login.php');
$usuario = $_SESSION['nome_usuario'];

//  CADASTRAR Orçamento
if (isset($_POST['button'])) {
    $cpf = $_POST['txtcpf'];
    $tecnico = $_POST['funcionario'];
    $produto = $_POST['txtproduto'];
    $serie = $_POST['txtserie'];
    $defeito = $_POST['txtdefeito'];
    $obs = $_POST['txtobs'];

    // Verifica se o cliente está cadastrado
    $query_verificar = "SELECT * FROM clientes WHERE cpf = '$cpf'";
    $result_verificar = mysqli_query($conexao, $query_verificar);
    $row_verificar = mysqli_num_rows($result_verificar);

    if ($row_verificar <= 0) {
        echo "<script>alert('O Cliente não está cadastrado!'); window.location.href = 'abrir_orcamentos.php';</script>";
        exit();
    }

    $query = "INSERT INTO orcamentos (cliente, tecnico, produto, serie, problema, obs, valor_total, data_abertura, status) 
              VALUES ('$cpf', '$tecnico', '$produto', '$serie', '$defeito', '$obs', '0', curDate(), 'Aberto')";

    $result = mysqli_query($conexao, $query);

    if (!$result) {
        echo "<script>alert('Ocorreu um erro ao Cadastrar!');</script>";
    } else {
        echo "<script>alert('Salvo com Sucesso!'); window.location.href = 'abrir_orcamentos.php';</script>";
    }
}

//  EXCLUIR Orçamento
if (isset($_GET['func']) && $_GET['func'] == 'deleta') {
    $id = $_GET['id'];
    $query = "DELETE FROM orcamentos WHERE id = '$id'";
    mysqli_query($conexao, $query);
    echo "<script>window.location='abrir_orcamentos.php';</script>";
}

// ✏️ EDITAR Orçamento
if (isset($_POST['buttonEditar'])) {
    $id = $_POST['id_orcamento'];
    $tecnico = $_POST['funcionario'];
    $produto = $_POST['txtproduto'];
    $serie = $_POST['txtserie'];
    $defeito = $_POST['txtdefeito'];
    $obs = $_POST['txtobs'];

    $query_editar = "UPDATE orcamentos SET tecnico = '$tecnico', produto = '$produto', 
                     serie = '$serie', problema = '$defeito', obs = '$obs' 
                     WHERE id = '$id'";

    $result_editar = mysqli_query($conexao, $query_editar);

    if (!$result_editar) {
        echo "<script>alert('Ocorreu um erro ao Editar!');</script>";
    } else {
        echo "<script>alert('Editado com Sucesso!'); window.location.href = 'abrir_orcamentos.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orçamentos</title>
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
                    <option value="Aberto">Aberto</option>
                    <option value="Aguardando">Aguardando</option>
                    <option value="Aprovado">Aprovado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
                <input type="date" name="txtpesquisar" placeholder="Pesquisar por data">
                <button type="submit" name="buttonPesquisar"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
    </header>

    <main class="container">
        <h2>Lista de Orçamentos</h2>
        <a class="btn" href="#" onclick="document.getElementById('modalCadastrar').style.display='flex'">+ Inserir Novo</a>
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Técnico</th>
                    <th>Produto</th>
                    <th>Valor Total</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // LISTAR TODOS OS ORÇAMENTOS
                $usuario = $_SESSION['nome_usuario'];

                if (isset($_GET['buttonPesquisar'])) {
                    $data = $_GET['txtpesquisar'];
                    $statusOrc = $_GET['status'];

                    $query = "SELECT o.id, o.cliente, o.tecnico, o.produto, o.valor_total, o.status, 
             c.nome as cli_nome, f.nome as func_nome 
             FROM orcamentos as o 
             INNER JOIN clientes as c ON o.cliente = c.cpf 
             INNER JOIN funcionarios as f ON o.tecnico = f.id";

                    $conditions = array();
                    $conditions[] = "f.nome = '$usuario'";

                    if (!empty($data)) {
                        $conditions[] = "o.data_abertura = '$data'";
                    }

                    if ($statusOrc != 'Todos') {
                        $conditions[] = "o.status = '$statusOrc'";
                    }

                    if (!empty($conditions)) {
                        $query .= " WHERE " . implode(' AND ', $conditions);
                    }

                    $query .= " ORDER BY o.id ASC";
                } else {
                    $query = "SELECT o.id, o.cliente, o.tecnico, o.produto, o.valor_total, o.status, 
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
                    echo "<tr><td colspan='6'><h3>Não existem dados cadastrados no banco</h3></td></tr>";
                } else {
                    while ($res_1 = mysqli_fetch_array($result)) {
                        $cliente = $res_1["cli_nome"];
                        $tecnico = $res_1["func_nome"];
                        $produto = $res_1["produto"];
                        $valor_total = $res_1["valor_total"];
                        $status = $res_1["status"];
                        $id = $res_1["id"];

                        echo "<tr>
                            <td>$cliente</td>
                            <td>$tecnico</td>
                            <td>$produto</td>
                            <td>$valor_total</td>
                            <td>$status</td>
                            <td class='actions'>
                                <a href='abrir_orcamentos.php?func=edita&id=$id'><i class='fa fa-edit'></i></a>
                                
                                <a href='abrir_orcamentos.php?func=deleta&id=$id'><i class='fa fa-trash'></i></a>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </main>

    <!-- Modal Cadastro -->
    <div id="modalCadastrar" class="modal">
        <div class="modal-content">
            <h3>Novo Orçamento</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="txtcpf">CPF</label>
                    <input type="text" id="txtcpf" name="txtcpf" placeholder="CPF" required>
                </div>

                <div class="form-group">
                    <label for="funcionario">Técnico</label>
                    <select id="funcionario" name="funcionario" required>
                        <?php
                        $query = "SELECT * FROM funcionarios WHERE cargo = 'Funcionário' ORDER BY nome ASC";
                        $result = mysqli_query($conexao, $query);

                        while ($res_1 = mysqli_fetch_array($result)) {
                            echo "<option value='{$res_1['id']}'>{$res_1['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="txtproduto">Produto</label>
                    <input type="text" id="txtproduto" name="txtproduto" placeholder="Produto" required>
                </div>

                <div class="form-group">
                    <label for="txtserie">Num Série</label>
                    <input type="text" id="txtserie" name="txtserie" placeholder="Número de Série" required>
                </div>

                <div class="form-group">
                    <label for="txtdefeito">Defeito</label>
                    <input type="text" id="txtdefeito" name="txtdefeito" placeholder="Defeito" required>
                </div>

                <div class="form-group">
                    <label for="txtobs">Observações</label>
                    <input type="text" id="txtobs" name="txtobs" placeholder="Observações" required>
                </div>

                <div class="botoes">
                    <button type="submit" name="button">Salvar</button>
                    <button type="button" onclick="document.getElementById('modalCadastrar').style.display='none'">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    // Modal de Edição
    if (isset($_GET['func']) && $_GET['func'] == 'edita') {
        $id = $_GET['id'];
        $query = "SELECT * FROM orcamentos WHERE id = '$id'";
        $result = mysqli_query($conexao, $query);
        $res_1 = mysqli_fetch_array($result);
    ?>
        <div id="modalEditar" class="modal" style="display:flex;">
            <div class="modal-content">
                <h3>Editar Orçamento</h3>
                <form method="POST">
                    <input type="hidden" name="id_orcamento" value="<?php echo $id; ?>">

                    <div class="form-group">
                        <label for="funcionario">Técnico</label>
                        <select id="funcionario" name="funcionario" required>
                            <?php
                            $query = "SELECT * FROM funcionarios WHERE cargo = 'Funcionário' ORDER BY nome ASC";
                            $result = mysqli_query($conexao, $query);

                            while ($res_2 = mysqli_fetch_array($result)) {
                                echo "<option value='{$res_2['id']}'>{$res_2['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="txtproduto">Produto</label>
                        <input type="text" id="txtproduto" name="txtproduto" value="<?php echo $res_1['produto']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="txtserie">Num Série</label>
                        <input type="text" id="txtserie" name="txtserie" value="<?php echo $res_1['serie']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="txtdefeito">Defeito</label>
                        <input type="text" id="txtdefeito" name="txtdefeito" value="<?php echo $res_1['problema']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="txtobs">Observações</label>
                        <input type="text" id="txtobs" name="txtobs" value="<?php echo $res_1['obs']; ?>" required>
                    </div>

                    <div class="botoes">
                        <button type="submit" name="buttonEditar">Salvar</button>
                        <button type="button" onclick="document.getElementById('modalEditar').style.display='none'">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.getElementById('modalEditar').style.display = 'flex';
        </script>
    <?php } ?>

    <script>
        $(document).ready(function() {
            $('#txtcpf').mask('000.000.000-00');
        });
    </script>

</body>

</html>