<?php
include('conexao.php');

// Função de sanitização
function limparEntrada($valor) {
    global $conexao;
    return mysqli_real_escape_string($conexao, htmlspecialchars(trim($valor)));
}

// Cadastrar usuário
if (isset($_POST['button'])) {
    $id_funcionario = limparEntrada($_POST['txtfuncionario']);
    $usuario = limparEntrada($_POST['txtusuario']);
    $senha = limparEntrada($_POST['txtsenha']);

    $query_func = mysqli_query($conexao, "SELECT nome, cargo FROM funcionarios WHERE id = '$id_funcionario'");
    $dados_func = mysqli_fetch_array($query_func);

    $nome = $dados_func['nome'] ?? '';
    $cargo = $dados_func['cargo'] ?? '';

    $verifica = mysqli_query($conexao, "SELECT * FROM usuarios WHERE usuario = '$usuario'");
    if (mysqli_num_rows($verifica) > 0) {
        echo "<script>alert('Usuário já cadastrado!');</script>";
    } else {
        $query = "INSERT INTO usuarios (nome, usuario, senha, cargo, id_funcionario) 
                  VALUES ('$nome', '$usuario', '$senha', '$cargo', '$id_funcionario')";
        $inserir = mysqli_query($conexao, $query);
        if ($inserir) {
            echo "<script>alert('Usuário cadastrado com sucesso!'); window.location='usuarios.php';</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar usuário!');</script>";
        }
    }
}

// Excluir usuário
if (@$_GET['func'] == 'deleta') {
    $id = limparEntrada($_GET['id']);
    mysqli_query($conexao, "DELETE FROM usuarios WHERE id = '$id'");
    echo "<script>window.location='usuarios.php';</script>";
}

// Editar usuário
if (isset($_POST['buttonEditar'])) {
    $id = limparEntrada($_POST['id_usuario']);
    $usuario = limparEntrada($_POST['txtusuario']);
    $senha = limparEntrada($_POST['txtsenha']);

    $verifica = mysqli_query($conexao, "SELECT * FROM usuarios WHERE usuario = '$usuario' AND id != '$id'");
    if (mysqli_num_rows($verifica) > 0) {
        echo "<script>alert('Nome de usuário já em uso por outro!');</script>";
    } else {
        $query = "UPDATE usuarios SET usuario = '$usuario', senha = '$senha' WHERE id = '$id'";
        $editar = mysqli_query($conexao, $query);
        if ($editar) {
            echo "<script>alert('Usuário atualizado com sucesso!'); window.location='usuarios.php';</script>";
        } else {
            echo "<script>alert('Erro ao editar usuário!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cssClientes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Usuários</title>
</head>
<body>
<header>
    <div class="voltar">
        <a href="painel_admin.php">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>
    <div class="pesquisar">
        <!-- Campo de busca para encontrar funcionario pelo nome -->
        <form class="campo-pesquisa" method="GET">
            <input type="text" name="txtpesquisar" placeholder="Pesquisar por nome">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button> <!-- Botão de busca -->
        </form>
    </div>
</header>
<main class="container">
    <h2>Lista de Usuários</h2>
    <a class="btn" href="#" onclick="document.getElementById('modalCadastrar').style.display='flex'">+ Novo Usuário</a>
    <table>
        <thead>
        <tr>
            <th>Nome</th>
            <th>Usuário</th>
            <th>Senha</th> 
            <th>Cargo</th>
            <th>Telefone</th>
            <th>Ações</th>
        </tr>

        </thead>
        <tbody>
        <?php
        $pesquisa = '';
        if (!empty($_GET['txtpesquisar'])) {
            $pesquisa = limparEntrada($_GET['txtpesquisar']);
            $query = "SELECT u.id, u.usuario, u.senha, f.nome, f.cargo, f.telefone 
                      FROM usuarios u 
                      INNER JOIN funcionarios f ON u.id_funcionario = f.id 
                      WHERE f.nome LIKE '%$pesquisa%'
                      ORDER BY f.nome ASC";
        } else {
            $query = "SELECT u.id, u.usuario, u.senha, f.nome, f.cargo, f.telefone 
                      FROM usuarios u 
                      INNER JOIN funcionarios f ON u.id_funcionario = f.id 
                      ORDER BY f.nome ASC";
        }
        

        $result = mysqli_query($conexao, $query);
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr>
                    <td>{$row['nome']}</td>
                    <td>{$row['usuario']}</td>
                    <td>{$row['senha']}</td> <!-- nova coluna -->
                    <td>{$row['cargo']}</td>
                    <td>{$row['telefone']}</td>
                    <td class='actions'>
                        <a href='usuarios.php?func=edita&id={$row['id']}'><i class='fa fa-edit'></i></a>
                        <a href='usuarios.php?func=deleta&id={$row['id']}' onclick=\"return confirm('Tem certeza que deseja excluir?');\"><i class='fa fa-trash'></i></a>
                    </td>
                </tr>";

        }
        ?>
        </tbody>
    </table>
</main>
<?php if (@$_GET['func'] == 'edita') {
    $id = limparEntrada($_GET['id']);
    $res = mysqli_query($conexao, "SELECT * FROM usuarios WHERE id = '$id'");
    $dados = mysqli_fetch_array($res);
?>
<div id="modalEditar" class="modal" style="display:flex;">
    <div class="modal-content">
        <h3>Editar Usuário</h3>
        <form method="POST">
            <input type="hidden" name="id_usuario" value="<?php echo $dados['id']; ?>">
            <input type="text" name="txtusuario" placeholder="Usuário" value="<?php echo $dados['usuario']; ?>" required>
            <input type="text" name="txtsenha" placeholder="Senha" value="<?php echo $dados['senha']; ?>" required>
            <div class="botoes">
                <button type="submit" name="buttonEditar">Atualizar</button>
                <button type="button" onclick="document.getElementById('modalEditar').style.display='none'">Cancelar</button>
            </div>
        </form>
    </div>
</div>
<?php } ?>
<div id="modalCadastrar" class="modal">
    <div class="modal-content">
        <h3>Usuário</h3>
        <form method="POST">
            <input type="text" name="txtusuario" placeholder="Usuário" required>
            <input type="password" name="txtsenha" placeholder="Senha" required>
            <select name="txtfuncionario" required>
                <option value="" disabled selected>Selecione o Funcionário</option>
                <?php
                $funcs = mysqli_query($conexao, "SELECT * FROM funcionarios ORDER BY nome ASC");
                while ($f = mysqli_fetch_array($funcs)) {
                    echo "<option value='{$f['id']}'>{$f['nome']}</option>";
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
