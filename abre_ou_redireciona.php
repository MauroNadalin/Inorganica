
<?php
// Verifique se o usuário está logado
include('config.php');
if (!usuarioLogado()) {
    header("Location: inicio.php");
exit();
}
?>
