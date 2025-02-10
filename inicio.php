<?php
include('config.php');
$_SESSION['passou_inicio'] = true;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inorgânica - Página Inicial</title>
    <link rel="stylesheet" href="style.css">
    <!-- Fonte Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="cabecalho">
            <div id="cabi">
                <img src="tampinha2.png" width="100" height="100" alt="PRI logo">
            </div>
            <div id="cabt">
                <h1>Bem-vindo à Reciclagem Inorgânica!</h1>
                
                <h4> Usuário Logdo: <?php if(usuarioLogado()){
 					     echo $_SESSION['user_nome'];
                                            } ?></h4>
                <h4> Perfil: <?php if(usuarioLogado()){
                                      echo $_SESSION['user_perfil'];
                                      } ?></h4>
                
                <h4>  Pontuação: <?php if(usuarioLogado()){
                                      echo $_SESSION['user_pontos'];
                                      } ?></h4></div>
           </div>
    </header>



    <?php if(usuarioLogado()): ?>


        <?php
           // Verifique se o perfil está na sessão e atribua o destino correto
           if ($_SESSION['user_perfil'] == "coletor"):
              $destino = "coletacoletor.php";
           else:
              $destino = "coletaprov.php";
           endif;
        ?>
    <nav>
        <ul class="menu">
            <li><a href="manual.pdf" target="_blank">Manual</a></li>
            <li><a href="cadusu.php">Cadastro de Usuários</a></li>
            <li><a href="<?php echo $destino; ?>">Coletas</a></li>
            <li><a href="relatorios.php">Relatorios</a></li>
            <li><a href="logout.php">Logout</a></li>

        </ul>
    </nav>

    <main>
        <section>
            <h2>Aqui você poderá saber mais sobre o programa, fazer seu cadastro, solicitar coletas e acessar o Manual.</h2>

      </section>
    <?php else: ?>
    <nav>
        <ul class="menu">
            <li><a href="manual.pdf" target="_blank">Manual</a></li>
            <li><a href="cadusu.php">Cadastro de Usuários</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>
    <main>
	<section>
	    <h2>Faça login para acessar mais opções</h2>
            <h2>Caso não tenha login, leia o manual,</h2>
            <h2>em seguida faça seu cadastro</h2>
        </section>
    <?php endif; ?>

    </main>

    <footer>
        <p>&copy; 2024 Todos os direitos reservados.</p>
    </footer>
</body>
</html>
