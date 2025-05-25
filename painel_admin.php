<?php 
    session_start();
    include('verificar_login.php');

    if($_SESSION['cargo_usuario'] != 'Administrador' && $_SESSION['cargo_usuario'] != 'Gerente'){
        header('Location: index.html');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cssPainelFunc.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <title>Painel Admin</title>
</head>
<body>
    <div class="menu-lateral">
      <div class="logo">GestVision</div>
        <ul>
          <li><a href="funcionarios.php"><i class="bi bi-person-badge"></i>Funcionários</a></li>

          <li><a href="usuarios.php"><i class="bi bi-people"></i>Usuários</a></li>

          <li><a href="cargos.php"><i class="bi bi-card-list"></i>Cargos</a></li>

          <li><a href="#"><i class="bi bi-cash-stack"></i> Abrir Orçamento</a></li>

          <li><a href="#"><i class="bi bi-box"></i> Fechar Orçamento</a></li>

          <li><a href="#"><i class="bi bi-file-earmark-text"></i> Relatório Orçamento</a></li>

          <li><a href="#"><i class="bi bi-bar-chart"></i> OS Abertos</a></li>

          <li><a href="#"><i class="bi bi-question-circle"></i> Consultar OS</a></li>
      </ul>
    </div>

    <header>

      <?php 
          if ($_SESSION['cargo_usuario'] == 'Gerente') {
      ?>
         <h2>Gerente<span style="color: #1ED760; font-weight: bold">
         <?php echo $_SESSION['nome_usuario']; ?></span></h2> 
      <?php 
        }else{
      ?> 
          <h2>Administrador(a)<span style="color: #1ED760; font-weight: bold">
          <?php echo $_SESSION['nome_usuario']; ?></span></h2>
      <?php 
        } 
      ?> 
      
      <div class="opcoes">
        <a class="btnMenu" href="#">Opções ▾</a>
        <div class="opcoes-menu">
          <a href="painel_tesouraria.php">Painel Financeiro</a>
          <a href="painel_funcionario.php">Painel Funcionário</a>
          <a href="index.html">Sair</a>
        </div>
      </div>
    </header>

      
</body>
</html>