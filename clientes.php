<?php
// Conectamos ao banco de dados onde os clientes são salvos
include('conexao.php');

// 👉 CADASTRAR CLIENTE
if (isset($_POST['button'])) { // Se o botão de salvar foi apertado
    $nome = $_POST['txtnome']; // Pegamos o nome digitado
    $telefone = $_POST['txttelefone']; // Pegamos o telefone digitado
    $endereco = $_POST['txtendereco']; // Pegamos o endereço digitado
    $email = $_POST['txtemail']; // Pegamos o email digitado
    $cpf = $_POST['txtcpf']; // Pegamos o CPF digitado

    // Verificamos se o email está no formato certo (ex: nome@site.com)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email inválido!');</script>"; // Se estiver errado, mostramos um alerta
    } else {
        // Procuramos no banco se já existe um cliente com o mesmo CPF
        $verifica = mysqli_query($conexao, "SELECT * FROM clientes WHERE cpf = '$cpf'");
        if (mysqli_num_rows($verifica) > 0) {
            echo "<script>alert('CPF já cadastrado!');</script>"; // Se existir, avisamos
        } else {
            // Se não existir, inserimos o novo cliente no banco
            $insere = mysqli_query($conexao, "INSERT INTO clientes (nome, telefone, endereco, email, cpf, data) VALUES ('$nome', '$telefone', '$endereco', '$email', '$cpf', curDate())");
            if ($insere) {
                echo "<script>alert('Salvo com Sucesso!'); window.location.href = 'clientes.php';</script>"; // Avisamos que salvou
            } else {
                echo "<script>alert('Erro ao salvar');</script>"; // Se der erro, avisamos
            }
        }
    }
}

// 🗑️ EXCLUIR CLIENTE
if (isset($_GET['func']) && $_GET['func'] == 'deleta') { // Se o usuário clicou em excluir
    $id = $_GET['id']; // Pegamos o ID do cliente
    mysqli_query($conexao, "DELETE FROM clientes WHERE id = '$id'"); // Apagamos do banco
    echo "<script>alert('Cliente Excluído'); window.location.href = 'clientes.php';</script>"; // Avisamos
}

// ✏️ EDITAR CLIENTE
if (isset($_POST['button_editar'])) { // Se clicou no botão de editar
    $id = $_POST['id_cliente']; // Pegamos o ID do cliente
    $nome = $_POST['txtnome_edit']; // Pegamos os novos dados preenchidos
    $telefone = $_POST['txttelefone_edit'];
    $endereco = $_POST['txtendereco_edit'];
    $email = $_POST['txtemail_edit'];
    $cpf = $_POST['txtcpf_edit'];

    // Verificamos se outro cliente já tem esse CPF
    $verifica = mysqli_query($conexao, "SELECT * FROM clientes WHERE cpf = '$cpf' AND id != '$id'");
    if (mysqli_num_rows($verifica) > 0) {
        echo "<script>alert('CPF já cadastrado em outro cliente!');</script>"; // Avisamos se tiver repetido
    } else {
        // Se estiver tudo certo, atualizamos os dados do cliente
        $editar = mysqli_query($conexao, "UPDATE clientes SET nome='$nome', telefone='$telefone', endereco='$endereco', email='$email', cpf='$cpf' WHERE id='$id'");
        if ($editar) {
            echo "<script>alert('Cliente atualizado com sucesso!'); window.location.href = 'clientes.php';</script>"; // Avisamos que salvou
        } else {
            echo "<script>alert('Erro ao editar cliente!');</script>"; // Se der erro, avisamos
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8"> <!-- Define que vamos usar acentos e cedilha -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Faz o site funcionar bem no celular -->
    
    <!-- Arquivo de estilos CSS para deixar a página bonita -->
    <link rel="stylesheet" href="cssClientes.css">

    <!-- Ícones prontos (como lápis, lixeira, lupa...) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Código JavaScript que ajuda a manipular a página -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Biblioteca para aplicar máscaras nos campos (ex: telefone, CPF) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <title>Clientes</title> <!-- Título que aparece na aba do navegador -->
</head>
<body>

<!-- Cabeçalho da página -->
<header>
    <div class="voltar">
        <!-- Botão para voltar para a tela anterior -->
        <a href="painel_funcionario.php">
            <i class="fa-solid fa-arrow-left"></i> <!-- Ícone de seta para trás -->
        </a>
    </div>

    <div class="pesquisar">
        <!-- Campo de busca para encontrar cliente pelo nome -->
        <form class="campo-pesquisa" method="GET">
            <input type="text" name="txtpesquisar" placeholder="Pesquisar por nome">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button> <!-- Botão de busca -->
        </form>
    </div>
</header>

<!-- Parte principal da página -->
<main class="container">
    <h2>Lista de Clientes</h2>
    
    <!-- Botão para abrir o formulário de novo cliente -->
    <a class="btn" href="#" onclick="document.getElementById('modalCadastrar').style.display='flex'">+ Novo Cliente</a>
    
    <!-- Tabela que mostra os clientes -->
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Endereço</th>
                <th>Email</th>
                <th>CPF</th>
                <th>Data</th>
                <th>Ações</th> <!-- Editar e excluir -->
            </tr>
        </thead>
        <tbody>
        <?php
        // Pegamos todos os clientes do banco e mostramos um por um
        $query = "SELECT * FROM clientes ORDER BY nome ASC";
        if (!empty($_GET['txtpesquisar'])) {
            $nome = $_GET['txtpesquisar'] . '%';
            $query = "SELECT * FROM clientes WHERE nome LIKE '$nome' ORDER BY nome ASC";
        }
        $result = mysqli_query($conexao, $query);
        while ($row = mysqli_fetch_array($result)) {
            // Formatamos a data (de 2025-05-07 para 07/05/2025)
            $data_formatada = implode('/', array_reverse(explode('-', $row['data'])));
            echo "<tr>
                <td>{$row['nome']}</td>
                <td>{$row['telefone']}</td>
                <td>{$row['endereco']}</td>
                <td>{$row['email']}</td>
                <td>{$row['cpf']}</td>
                <td>$data_formatada</td>
                <td class='actions'>
                    <a href='clientes.php?func=edita&id={$row['id']}'><i class='fa fa-edit'></i></a> <!-- Botão de editar -->
                    <a href='?func=deleta&id={$row['id']}'><i class='fa fa-trash'></i></a> <!-- Botão de excluir -->
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
    $result = mysqli_query($conexao, "SELECT * FROM clientes WHERE id = '$id'");
    $res_1 = mysqli_fetch_array($result);
?>
<!-- Janela flutuante com os dados preenchidos do cliente -->
<div id="modalEditar" class="modal" style="display:flex;">
    <div class="modal-content">
        <h3>Editar Cliente</h3>
        <form method="POST">
            <input type="hidden" name="id_cliente" value="<?php echo $res_1['id']; ?>">
            <input type="text" name="txtnome_edit" placeholder="Nome" value="<?php echo $res_1['nome']; ?>" required>
            <input type="text" name="txttelefone_edit" id="txttelefone_edit" placeholder="Telefone" value="<?php echo $res_1['telefone']; ?>" required>
            <input type="text" name="txtendereco_edit" placeholder="Endereço" value="<?php echo $res_1['endereco']; ?>" required>
            <input type="email" name="txtemail_edit" placeholder="Email" value="<?php echo $res_1['email']; ?>" required>
            <input type="text" name="txtcpf_edit" id="txtcpf_edit" placeholder="CPF" value="<?php echo $res_1['cpf']; ?>" required>
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
        <h3>Novo Cliente</h3>
        <form method="POST">
            <input type="text" name="txtnome" placeholder="Nome" required>
            <input type="text" name="txttelefone" id="txttelefone" placeholder="Telefone" required>
            <input type="text" name="txtendereco" placeholder="Endereço" required>
            <input type="email" name="txtemail" placeholder="Email" required>
            <input type="text" name="txtcpf" id="txtcpf" placeholder="CPF" required>
            <div class="botoes">
                <button type="submit" name="button">Salvar</button>
                <button type="button" onclick="document.getElementById('modalCadastrar').style.display='none'">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#txttelefone').mask('(00) 00000-0000');
    $('#txtcpf').mask('000.000.000-00');
    $('#txttelefone_edit').mask('(00) 00000-0000');
    $('#txtcpf_edit').mask('000.000.000-00');
});
</script>

</body>
</html>
