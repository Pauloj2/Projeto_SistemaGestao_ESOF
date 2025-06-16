<?php
session_start();
include('../config/conexao.php');

// Cancelar orçamentos com mais de 5 dias aguardando
$query_cancelar = "
    UPDATE orcamentos 
    SET status = 'Cancelado' 
    WHERE status = 'Aguardando' 
    AND data_geracao <= DATE_SUB(CURDATE(), INTERVAL 5 DAY)
";
mysqli_query($conexao, $query_cancelar);




if (!isset($_POST['usuario']) || !isset($_POST['senha'])) {
    header('Location: index.php');
    exit();
}

$usuario = trim($_POST['usuario']);
$senha = trim($_POST['senha']);


if (empty($usuario) || empty($senha)) {
    $_SESSION['login_erro'] = true;
    header('Location: ../index.php');
    exit();
}

$usuario = mysqli_real_escape_string($conexao, $usuario);
$senha = mysqli_real_escape_string($conexao, $senha);

$query = "SELECT * FROM usuarios WHERE usuario = '{$usuario}' AND senha = '{$senha}'";
$result = mysqli_query($conexao, $query);
$dado = mysqli_fetch_array($result);
$row = mysqli_num_rows($result);

if ($row > 0) {
    $_SESSION['usuario'] = $usuario;
    $_SESSION['nome_usuario'] = $dado["nome"];
    $_SESSION['cargo_usuario'] = $dado["cargo"];
    $_SESSION['id_usuario'] = $dado["id"];

    if ($_SESSION['cargo_usuario'] == 'Administrador' || $_SESSION['cargo_usuario'] == 'Gerente') {
        header('Location: ../views/painel_admin.php');
    } elseif ($_SESSION['cargo_usuario'] == 'Tesoureiro') {
        header('Location: ../views/painel_tesouraria.php');
    } elseif ($_SESSION['cargo_usuario'] == 'Funcionario') {
        header('Location: ../views/painel_funcionario.php');
    }
    exit();
} else {
    $_SESSION['login_erro'] = true;
    header('Location: index.php');
    exit();
}
