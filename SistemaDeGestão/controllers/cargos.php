<?php
include('../config/conexao.php');

// CADASTRAR
if (isset($_POST['button'])) {
    $nome = trim($_POST['txtnome']);

    $query_verificar = "SELECT * FROM cargos WHERE cargo = '$nome'";
    $result_verificar = mysqli_query($conexao, $query_verificar);

    if (mysqli_num_rows($result_verificar) > 0) {
        echo "<script>alert('Cargo já Cadastrado!');</script>";
    } else {
        $query = "INSERT INTO cargos (cargo) VALUES ('$nome')";
        $result = mysqli_query($conexao, $query);

        if ($result) {
            echo "<script>alert('Salvo com Sucesso!'); window.location='cargos.php';</script>";
        } else {
            echo "<script>alert('Erro ao Cadastrar!');</script>";
        }
    }
}

// EXCLUIR
if (@$_GET['func'] == 'deleta') {
    $id = $_GET['id'];
    $query = "DELETE FROM cargos WHERE id = '$id'";
    mysqli_query($conexao, $query);
    echo "<script>window.location='cargos.php';</script>";
}

// EDITAR
if (@$_GET['func'] == 'edita') {
    $id = $_GET['id'];
    $query = "SELECT * FROM cargos WHERE id = '$id'";
    $result = mysqli_query($conexao, $query);
    $res_1 = mysqli_fetch_array($result);

    if (!$res_1) {
        echo "<script>alert('Cargo não encontrado!'); window.location='cargos.php';</script>";
        exit();
    }

    if (isset($_POST['buttonEditar'])) {
        $novo_nome = trim($_POST['txtnome']);

        // Verifica se já existe outro com o mesmo nome
        $query_verifica = "SELECT * FROM cargos WHERE cargo = '$novo_nome' AND id != '$id'";
        $result_verifica = mysqli_query($conexao, $query_verifica);

        if (mysqli_num_rows($result_verifica) > 0) {
            echo "<script>alert('Cargo já cadastrado!');</script>";
        } else {
            $query_editar = "UPDATE cargos SET cargo = '$novo_nome' WHERE id = '$id'";
            $result_editar = mysqli_query($conexao, $query_editar);

            if ($result_editar) {
                echo "<script>alert('Editado com Sucesso!'); window.location='cargos.php';</script>";
            } else {
                echo "<script>alert('Erro ao Editar!');</script>";
            }
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
    <link rel="stylesheet" href="../assets/cssClientes.css">

    <!-- Ícones prontos (como lápis, lixeira, lupa...) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Código JavaScript que ajuda a manipular a página -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Biblioteca para aplicar máscaras nos campos (ex: telefone, CPF) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <title>Cargos</title> <!-- Título que aparece na aba do navegador -->
</head>
<body>

<!-- Cabeçalho da página -->
<header>
    <div class="voltar">
        <!-- Botão para voltar para a tela anterior -->
        <a href="../views/painel_admin.php">
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
    <h2>Lista de Cargos</h2>
    
    <!-- Botão para abrir o formulário de novo cliente -->
    <a class="btn" href="#" onclick="document.getElementById('modalCadastrar').style.display='flex'">+ Novo Cargo</a>
    
    <!-- Tabela que mostra os clientes -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cargo</th>
                <th>Ações</th> <!-- Editar e excluir -->
            </tr>
        </thead>
        <tbody>
        <?php
        // Pegamos todos os clientes do banco e mostramos um por um
        $query = "SELECT * FROM cargos ORDER BY cargo ASC";
        if (!empty($_GET['txtpesquisar'])) {
            $nome = $_GET['txtpesquisar'] . '%';
            $query = "SELECT * FROM cargos WHERE cargo LIKE '$nome' ORDER BY cargo ASC";
        }
        $result = mysqli_query($conexao, $query);
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['cargo']}</td>
                <td class='actions'>
                    <a href='cargos.php?func=edita&id={$row['id']}'><i class='fa fa-edit'></i></a> <!-- Botão de editar -->
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
    $result = mysqli_query($conexao, "SELECT * FROM cargos WHERE id = '$id'");
    $res_1 = mysqli_fetch_array($result);
?>
<!-- Janela flutuante com os dados preenchidos do cliente -->
<div id="modalEditar" class="modal" style="display:flex;">
    <div class="modal-content">
        <h3>Editar Cargo</h3>
        <form method="POST">
            <input type="hidden" name="id_cliente" value="<?php echo $res_1['id']; ?>">
            <input type="text" name="txtnome" placeholder="Cargo" value="<?php echo $res_1['cargo']; ?>" required>
            <div class="botoes">
                <button type="submit" name="buttonEditar">Atualizar</button>
                <button type="button" onclick="document.getElementById('modalEditar').style.display='none'">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<?php } ?>


<!-- Modal Cadastro -->
<div id="modalCadastrar" class="modal">
    <div class="modal-content">
        <h3>Cargos</h3>
        <form method="POST">
            <input type="text" name="txtnome" placeholder="Cargo" required>
        
            <div class="botoes">
                <button type="submit" name="button">Salvar</button>
                <button type="button" onclick="document.getElementById('modalCadastrar').style.display='none'">Cancelar</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
