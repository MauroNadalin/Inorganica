

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inorganica - Cadastro Usuário</title>
    <link rel="stylesheet" href="coletacoletor3.css">
</head>
<body>

<header>
    <div class="cabecalho">
        <div id="cabi">
            <img src="tampinha2.png" width="100" height="100" alt="PRI logo">
        </div>
        <div id="cabt">
            <h1>Tratativa de Coletas</h1>
            <a href="#" class="btn" onclick="window.location.reload();">Atualizar Dados</a>
            <button class="btn" type="button" onclick="window.location.href='inicio.php'">Voltar</button>
        </div>
    </div>
</header>

<?php

// Iniciar sessão para acessar o ID do usuário logado
//include('config.php');
include('conexao.php');
include('abre_ou_redireciona.php');
// Verifique se o usuário está logado
//if (!usuariologado()) {
  //  header("Location: login.php");
//exit();
//}

$user_id = $_SESSION['user_id']; // O ID do usuário logado
$user_perfil= $_SESSION['user_perfil']; // O perfil do usuário logado: provedor ou coletor.
// Conexão com o banco de dados
$dsn = 'mysql:host=localhost;dbname=inodb';
$username = 'root';
$password = 'xxxxxxxx';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL para buscar as coletas, incluindo os nomes de provedor e coletor
    $sql = "
        SELECT c.id, p.nome AS provedor, u.nome AS coletor, c.endereco,
               c.data_provisionamento, c.periodo_inicio, c.periodo_fim, c.status, c.coletor_id
        FROM coletas c
        LEFT JOIN users p ON c.provedor_id = p.id
        LEFT JOIN users u ON c.coletor_id = u.id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Exibindo os resultados em uma tabela HTML

    echo "<table border='1'>
            <thead>
                <tr>
                    <th>Provedor</th>
                    <th>Endereço</th>
                    <th>Data de Provisionamento</th>
                    <th>Período Início</th>
                    <th>Período Fim</th>
                    <th>Status</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>";

    // Loop para exibir cada linha de resultado
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $options = '';
        // Verificar o status e o coletor para definir as opções
        if ($row['status'] !== 'coletado' && $row['status'] !== 'excluído'){
        if ($row['coletor'] === null && $row['status'] === 'disponivel') {
            // Se o campo 'coletor' for nulo e 'status' for "disponível", mostrar apenas "Reservar"
            $options = "<option value='reservar'>Reservar</option>";
        } elseif ($row['coletor_id'] == $user_id) {
            // Se o usuário logado é o coletor, mostrar "Coletar" e "Cancelar reserva"
            $options = "<option value='coletar'>Coletar</option>
                        <option value='cancelar_reserva'>Cancelar Reserva</option>";
        } else {
            // Caso contrário, mostrar "Sem ação"
            $options = "<option value='sem_acao'>Sem ação</option>";
        }
     }

        	if ($row['status'] !== 'coletado' && $row['status'] !== 'excluído'){

	        echo


		     "<tr>
               		 <td>" . htmlspecialchars($row['provedor']) . "</td>
               		 <td>" . htmlspecialchars($row['endereco']) . "</td>
               		 <td>" . htmlspecialchars($row['data_provisionamento']) . "</td>
               		 <td>" . htmlspecialchars($row['periodo_inicio']) . "</td>
               		 <td>" . htmlspecialchars($row['periodo_fim']) . "</td>
               		 <td>" . htmlspecialchars($row['status']) . "</td>
               		 <td>
                   	 <form method='POST'>
                       	 <input type='hidden' name='coleta_id' value='" . $row['id'] . "' />
                       	 <select name='acao'>
                            $options
                        </select>
                        <input type='submit' value='Executar' />;
                   	 </form>
                	</td>
             	     </tr>";
                  }
    }
    echo "</tbody>
        </table>";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>

<?php
// Processar as ações quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['coleta_id']) && isset($_POST['acao'])) {
        $coleta_id = $_POST['coleta_id'];
        $acao = $_POST['acao'];

        try {
            if ($acao == 'reservar') {
                // Reservar: Definir o coletor como o usuário logado e mudar o status para "reservada"
                $update_sql = "
                    UPDATE coletas
                    SET status = 'reservada', coletor_id = :coletor_id, data_reserva = NOW()
                    WHERE id = :coleta_id";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->execute([
                    ':coletor_id' => $user_id,
                    ':coleta_id' => $coleta_id
                ]);
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;

            } elseif ($acao == 'cancelar_reserva') {
                // Cancelar Reserva: Remover o coletor e mudar o status para "disponível"
                $update_sql = "
                    UPDATE coletas
                    SET status = 'disponivel', coletor_id = NULL
                    WHERE id = :coleta_id";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->execute([':coleta_id' => $coleta_id]);
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;

            } elseif ($acao == 'coletar') {
                // Coletar: Definir a data da coleta e mudar o status para "coletado"
                $update_sql = "
                    UPDATE coletas
                    SET status = 'coletado', data_coleta = NOW()
                    WHERE id = :coleta_id";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->execute([':coleta_id' => $coleta_id]);

                // Código JavaScript para abrir o prompt com a nota após a ação ser executada
                echo "<script>
                        // Perguntar a nota para o usuário

                        let nota = prompt('Dê uma nota de 1 a 5');

                        // Garantir que o valor digitado seja um número inteiro
                        nota = parseInt(nota, 10);

                        // Validar se a nota está dentro do intervalo de 1 a 5
                        if (isNaN(nota) || nota < 1 || nota > 5) {
                        alert('Nota inválida. Por favor, insira um valor entre 1 e 5.');
                        } else {
                        alert('Você deu a nota: ' + nota); 

                       }     
                            // Enviar nota ao servidor para somar aos pontos do provedor
                            const coletaId = " . $coleta_id . "; // A coleta ID do PHP para saber a qual coleta estamos nos referindo
                            // Usar AJAX ou redirecionamento para processar a nota
                            window.location.href = 'processar_nota.php?nota=' + nota + '&coleta_id=' + coletaId;
                      </script>";
                      
                exit;
           } 

        } catch (PDOException $e) {
            echo "Erro ao processar a ação: " . $e->getMessage();
        }
    }
}
?>
</body>
</html>

