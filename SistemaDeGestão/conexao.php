<?php 

define('HOST', 'localhost:3307');
define('USUARIO', 'root');
define('SENHA', '');
define('BD', 'helpcash');

$conexao = mysqli_connect(HOST, USUARIO, SENHA, BD) or die ('Não Conectou!');

?>