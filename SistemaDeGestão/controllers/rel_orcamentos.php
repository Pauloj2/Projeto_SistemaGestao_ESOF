<?php
include('../config/conexao.php');
session_start();
include('verificar_login.php');

$usuario = $_SESSION['nome_usuario'];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório do Orçamento</title>
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
                <select name="status" id="status">
                    <option value="Aguardando" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Aguardando') ? 'selected' : ''; ?>>Aguardando</option>
                    <option value="Aprovado" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Aprovado') ? 'selected' : ''; ?>>Aprovado</option>
                </select>
                <input type="date" name="txtpesquisar" value="<?php echo isset($_GET['txtpesquisar']) ? $_GET['txtpesquisar'] : ''; ?>">
                <button type="submit" name="buttonPesquisar"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
    </header>

    <main class="container">
        <h2>Lista de Orçamentos</h2>

        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Técnico</th>
                    <th>Produto</th>
                    <th>Valor Total</th>
                    <th>Telefone</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $status = isset($_GET['status']) ? $_GET['status'] : 'Aguardando';
                $data = isset($_GET['txtpesquisar']) ? $_GET['txtpesquisar'] : '';

                $query = "SELECT o.id, o.cliente, o.tecnico, o.produto, o.valor_total, o.status, o.data_abertura, 
                          c.nome AS cli_nome, c.telefone, f.nome AS func_nome
                          FROM orcamentos AS o
                          INNER JOIN clientes AS c ON o.cliente = c.cpf
                          INNER JOIN funcionarios AS f ON o.tecnico = f.id
                          WHERE o.status = '$status'";

                // Removi a condição f.nome = '$usuario' para mostrar todos os orçamentos
                // Se quiser filtrar apenas pelo usuário logado, descomente a linha abaixo
                // $query .= " AND f.nome = '$usuario'";

                if (!empty($data)) {
                    $query .= " AND o.data_abertura = '$data'";
                }

                $query .= " ORDER BY o.data_abertura DESC";

                $result = mysqli_query($conexao, $query);

                if (!$result) {
                    echo "<tr><td colspan='7'>Erro na consulta: " . mysqli_error($conexao) . "</td></tr>";
                } else {
                    $row = mysqli_num_rows($result);

                    if ($row <= 0) {
                        echo "<tr><td colspan='7'><h3>Não existem dados cadastrados</h3></td></tr>";
                    } else {
                        while ($res_1 = mysqli_fetch_array($result)) {
                            $cliente = $res_1["cli_nome"];
                            $tecnico = $res_1["func_nome"];
                            $produto = $res_1["produto"];
                            $valor_total = number_format($res_1["valor_total"], 2, ',', '.');
                            $tel = $res_1["telefone"];
                            $data_abertura = date('d/m/Y', strtotime($res_1["data_abertura"]));
                            $id = $res_1["id"];

                            echo "<tr>
                                    <td>$cliente</td>
                                    <td>$tecnico</td>
                                    <td>$produto</td>
                                    <td>R$ $valor_total</td>
                                    <td>$tel</td>
                                    <td>$data_abertura</td>
                                    <td class='actions'>
                                        <a href='rel/rel_orcamento_class.php?id=$id' title='Visualizar'><i class='fas fa-file-alt'></i></a>";

                            if ($status == 'Aguardando') {
                                echo "<a href='rel_orcamentos.php?func=edita&id=$id' title='Aprovar'><i class='fas fa-check-circle' style='color: green'></i></a>";
                                echo "<a href='rel_orcamentos.php?func=deleta&id=$id' title='Excluir Orçamento' onclick=\"return confirm('Tem certeza que o cliente não aceitou a Ordem de Serviço?');\"><i class='fa fa-trash' style='color: red;'></i></a>";
                            }

                            echo "</td></tr>";
                        }
                    }
                }
                ?>
            </tbody>
        </table>
    </main>
</body>

</html>
<?php
if (isset($_GET['func']) && $_GET['func'] == 'deleta') {
    $id = $_GET['id'];

    // PREPARED STATEMENT para evitar SQL Injection
    $stmt = $conexao->prepare("UPDATE orcamentos SET status = 'Cancelado' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    echo "<script language='javascript'> window.location='rel_orcamentos.php'; </script>";
    exit(); // Importante para parar a execução após o redirect
}
?>

<?php
if (isset($_GET['func']) && $_GET['func'] == 'edita') {
    $id = $_GET['id'];
    $query = "SELECT * FROM orcamentos WHERE id = '$id'";
    $result = mysqli_query($conexao, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $res_1 = mysqli_fetch_array($result);
        $total = $res_1['valor_total'];
        $cliente = $res_1['cliente'];
        $produto = $res_1['produto'];
        $tecnico = $res_1['tecnico'];
?>

        <div id="modalEditar" class="modal">
            <div class="modal-content">
                <span class="close" onclick="document.getElementById('modalEditar').style.display='none'">&times;</span>
                <h3>Aprovar Orçamento</h3>
                <form method="POST">
                    <input type="hidden" name="id_orcamento" value="<?php echo $id; ?>">

                    <div class="form-group">
                        <label for="pgto">Forma de Pagamento</label>
                        <select id="pgto" name="pgto" required>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Cartão">Cartão</option>
                            <option value="Pix">Pix</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="txtdesconto">Desconto (R$)</label>
                        <input type="number" id="txtdesconto" name="txtdesconto" value="0" min="0" max="<?php echo $total; ?>" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label>Valor Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></label>
                    </div>

                    <div class="form-group">
                        <label>Valor Final: <span id="valorFinal">R$ <?php echo number_format($total, 2, ',', '.'); ?></span></label>
                    </div>

                    <div class="botoes">
                        <button type="submit" name="buttonEditar">Aprovar</button>
                        <button type="button" onclick="document.getElementById('modalEditar').style.display='none'">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.getElementById('modalEditar').style.display = 'flex';

            // Atualiza o valor final quando o desconto muda
            document.getElementById('txtdesconto').addEventListener('input', function() {
                const total = <?php echo $total; ?>;
                const desconto = parseFloat(this.value) || 0;
                const valorFinal = total - desconto;

                if (valorFinal < 0) {
                    this.value = total;
                    document.getElementById('valorFinal').textContent = 'R$ 0,00';
                } else {
                    document.getElementById('valorFinal').textContent = 'R$ ' + valorFinal.toFixed(2).replace('.', ',');
                }
            });
        </script>

<?php
    }

    if (isset($_POST['buttonEditar'])) {
        $pgto = $_POST['pgto'];
        $desconto = floatval($_POST['txtdesconto']);
        $valor_final = floatval($total) - $desconto;

        if ($valor_final < 0) {
            $valor_final = 0;
            $desconto = $total;
        }

        $query_editar = "UPDATE orcamentos SET 
                         status = 'Aprovado',
                         desconto = '$desconto', 
                         valor_total = '$valor_final', 
                         pgto = '$pgto', 
                         data_aprovacao = CURDATE() 
                         WHERE id = '$id'";

        $result_editar = mysqli_query($conexao, $query_editar);

        //Fazer a Abertura da OS

        $query_os = "Insert into os (id_orc, cliente, produto, tecnico, total, data_abertura, status) Values ('$id', '$cliente','$produto', '$tecnico', '$valor_total', curDate(), 'Aberta')";

        mysqli_query($conexao, $query_os);

        if (!$result_editar) {
            echo "<script>alert('Erro ao aprovar orçamento: " . mysqli_error($conexao) . "');</script>";
        } else {
            echo "<script>alert('Orçamento aprovado com sucesso!'); window.location.href = 'rel_orcamentos.php';</script>";
        }
    }
}
?>