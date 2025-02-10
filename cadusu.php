<?php
require('config.php');
if (!$_SESSION['passou_inicio']){
header("Location: inicio.php");
exit();
}


// Habilitando exibição de erros
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuração do banco de dados
$servername = "localhost";
$username = "root";
$password = "mAu52gro$$0";
$dbname = "inodb";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Variável para mensagem
$message = "";

// Verificando se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitização dos dados
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $perfil = $_POST['perfil'];
    $email = $_POST['email'];
    $rua = $_POST['rua'];
    $numero = $_POST['numero'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $login = $_POST['login'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografando a senha


    // Verificando se o login já existe no banco de dados
    $checkLoginQuery = "SELECT id FROM users WHERE login = ?";
    $stmt = $conn->prepare($checkLoginQuery);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->store_result();
   
    if ($stmt->num_rows > 0) {
        $message = "Erro: O login '$login' já está em uso. Escolha outro login.";
    } else {
        // Inserindo os dados na tabela 'users'
        $sql = "INSERT INTO users (nome, cpf, perfil, email, rua, numero, bairro, cidade, estado, login, senha)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Preparando a declaração SQL
        $stmtInsert = $conn->prepare($sql);
        $stmtInsert->bind_param("sssssssssss", $nome, $cpf, $perfil, $email, $rua, $numero, $bairro, $cidade, $estado, $login, $senha);

        if ($stmtInsert->execute()) {
            $message = "Cadastro realizado com sucesso!";
        } else {
            $message = "Erro: " . $stmtInsert->error;
        }

        // Fechando a declaração de inserção
        $stmtInsert->close();
    }

    // Fechando a declaração de verificação de login
    $stmt->close();
}

// Fechando a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inorganica - Cadastro Usuário</title>
    <link rel="stylesheet" href="cadusu.css">
<script>
        // Função para formatar o CPF conforme o usuário digita
        function formatarCPF(campo) {
            var cpf = campo.value.replace(/\D/g, ''); // Remove qualquer caractere não numérico
            if (cpf.length <= 3) {
                campo.value = cpf;
            } else if (cpf.length <= 6) {
                campo.value = cpf.slice(0, 3) + '.' + cpf.slice(3);
            } else if (cpf.length <= 9) {
                campo.value = cpf.slice(0, 3) + '.' + cpf.slice(3, 6) + '.' + cpf.slice(6);
            } else {
                campo.value = cpf.slice(0, 3) + '.' + cpf.slice(3, 6) + '.' + cpf.slice(6, 9) + '-' + cpf.slice(9, 11);
            }
        }
    </script>


</head>
<body>

<header>
    <div class="cabecalho">
        <div id="cabi">
            <img src="tampinha2.png" width="100" height="100" alt="PRI logo">
        </div>
        <div id="cabt">
            <h1>Cadastro de Usuários</h1>
            <button type="button" onclick="window.location.href='inicio.php'">Voltar</button>

        </div>
    </div>
</header>

<section class="form-section">
    <form method="POST" action="">
        <div class="form-titulo">
            <h2>Insira os dados do usuário</h2>
        </div>

        <div class="form-fields">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? $_POST['nome'] : ''; ?>" required>

            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" oninput="formatarCPF(this)" value="<?php echo isset($_POST['cpf']) ? $_POST['cpf'] : ''; ?>"  onblur="validarCPF()"  required>
            <span id="mensagemCpf" style="color: red;"></span> <!-- Mensagem de erro do CPF -->
            <label for="perfil">Perfil:</label>
            <select id="perfil" name="perfil">
                <option value="provedor" <?php echo (isset($_POST['perfil']) && $_POST['perfil'] == 'provedor') ? 'selected' : ''; ?>>Provedor</option>
                <option value="coletor" <?php echo (isset($_POST['perfil']) && $_POST['perfil'] == 'coletor') ? 'selected' : ''; ?>>Coletor</option>
            </select>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>  

            <label for="rua">Rua:</label>
            <input type="text" id="rua" name="rua" value="<?php echo isset($_POST['rua']) ? $_POST['rua'] : ''; ?>" required>      

            <label for="numero">Número:</label>
            <input type="text" id="numero" name="numero" value="<?php echo isset($_POST['numero']) ? $_POST['numero'] : ''; ?>" required>

            <label for="bairro">Bairro:</label>
            <input type="text" id="bairro" name="bairro" value="<?php echo isset($_POST['bairro']) ? $_POST['bairro'] : ''; ?>" required>

            <label for="cidade">Cidade:</label>
            <input type="text" id="cidade" name="cidade" value="<?php echo isset($_POST['cidade']) ? $_POST['cidade'] : ''; ?>" required>

            <label for="estado">Estado:</label>
            <input type="text" id="estado" name="estado" value="<?php echo isset($_POST['estado']) ? $_POST['estado'] : ''; ?>" required>

            <label for="login">Login:</label>
            <input type="text" id="login" name="login" value="<?php echo isset($_POST['login']) ? $_POST['login'] : ''; ?>" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
        </div>

        <div class="form-actions">
            <input type="submit" value="Cadastrar">
        </div>
    </form>
</section>

<!-- Modal de Mensagem -->
<div id="myModal" class="modal">
    <div class="modal-content <?php echo $message ? ($message == "Cadastro realizado com sucesso!" ? 'modal-success' : 'modal-error') : ''; ?>">
        <span class="close">&times;</span>
        <p id="modalMessage"><?php echo $message ? $message : ''; ?></p>
    </div>
</div>

<script>
    // Mostrar o modal se houver mensagem
    <?php if ($message != ""): ?>
        var modal = document.getElementById("myModal");
        modal.style.display = "block";
    <?php endif; ?>

    // Fechar o modal quando clicar no "X"
    var span = document.getElementsByClassName("close")[0];
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Fechar o modal se clicar fora dele
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<script>
function validarCPF() {
        var cpf = document.getElementById('cpf').value;
        var mensagemCpf = document.getElementById('mensagemCpf');
        
        // Remover caracteres não numéricos
        cpf = cpf.replace(/[^\d]+/g, '');

        // Verificar se o CPF tem 11 dígitos
        if (cpf.length !== 11 || !validarCPFNumeros(cpf)) {
            // Exibe a mensagem de erro e limpa o campo
            mensagemCpf.textContent = "CPF inválido!";
            document.getElementById('cpf').value = ''; // Limpa o campo CPF
        } else {
            mensagemCpf.textContent = ''; // Limpa a mensagem de erro
        }
    }






 // Função para validar os números do CPF
    function validarCPFNumeros(cpf) {
        var soma = 0;
        var resto;
        if (cpf == "00000000000") return false;
        
        for (var i = 1; i <= 9; i++) {
            soma += parseInt(cpf.charAt(i-1)) * (11 - i);
        }
        resto = (soma * 10) % 11;
        
        if (resto == 10 || resto == 11) resto = 0;
        if (resto != parseInt(cpf.charAt(9))) return false;
        
        soma = 0;
        for (var i = 1; i <= 10; i++) {
            soma += parseInt(cpf.charAt(i-1)) * (12 - i);
        }
        resto = (soma * 10) % 11;
        
        if (resto == 10 || resto == 11) resto = 0;
        if (resto != parseInt(cpf.charAt(10))) return false;
        
        return true;
    }
</script>

</body>
</html>
