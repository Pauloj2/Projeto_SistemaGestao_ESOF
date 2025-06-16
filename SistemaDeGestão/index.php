<?php
session_start();
$erroLogin = false;

// Exibir o erro somente após uma tentativa inválida
if (isset($_SESSION['login_erro']) && $_SESSION['login_erro'] === true) {
  $erroLogin = true;
  unset($_SESSION['login_erro']); // Limpa para não exibir novamente
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/cssLogin.css">
  <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css">
  <title>Login</title>
</head>

<body>
  <form action="controllers/login.php" method="post">
    <section class="login">
      <div class="formulario">
        <h2>
          
          LOGIN
        </h2>
        <p>Acesse sua conta e mantenha tudo sob controle.</p>

        <?php if ($erroLogin): ?>
          <p style="color: red; text-align: center;"><small>Usuário ou Senha Inválidos!</small></p>
        <?php endif; ?>


        <label for="usuario">Usuário</label>
        <input type="text" id="usuario" name="usuario" required>

        <br><br>

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required>

        <br><br>

        <button type="submit">Entrar</button>
      </div>
    </section>
  </form>
</body>

</html>