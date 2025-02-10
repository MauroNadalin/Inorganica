<?php
include('config.php');
if (!$_SESSION['passou_inicio']){
header("Location: inicio.php");
exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    // Busca o usuário no banco de dados
    $sql = "SELECT * FROM users WHERE login = :login";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['login' => $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário existe e a senha está correta
	
    if ($user && password_verify($senha, $user['senha'])) {
        // Inicia a sessão e armazena os dados do usuário
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_login'] = $user['login'];
        $_SESSION['user_perfil'] = $user['perfil'];
        $_SESSION['user_pontos'] = $user['pontos'];
        $_SESSION['user_nome'] = $user['nome'];
        header('Location: inicio.php'); // Redireciona para a página principal
        exit;
    } else {
        $erro = "Usuário ou senha inválidos!";//.$user['senha']." --- ".$senha;
	
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<header>
    <div class="cabecalho">
        <div id="cabi">
            <img src="tampinha2.png" width="100" height="100" alt="PRI logo">
        </div>
        <div id="cabt">
            <h1>Login</h1>
        </div>
    </div>
</header>
<section class="form-section">
    <?php if (isset($erro)) echo "<p>$erro</p>"; ?>
      <form method="post">
        <div class="form-titulo">
                 <h2>Login</h2>
        </div>
      
        <div class="form-fields"> 
          <label for="login">Login:</label>
          <input type="text" name="login" required><br>
          <label for="senha">Senha:</label>
          <input type="password" name="senha" required><br>
        </div>   
        <div class="form-actions">
          <button type="submit">Entrar</button>
        </div>
    </form>
</section>
</body>
</html>
