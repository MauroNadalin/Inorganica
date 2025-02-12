<?php
//Iniciar sessão para acessar o ID do usuário logado
//include('config.php');
include('conexao.php');
include('abre_ou_redireciona.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inorganica - Relatorios de Coletoas</title>
    <link rel="stylesheet" href="relatorios3.css">
</head>
<body>

<header>
            <form method="GET" action="exportar.php">

    <div class="cabecalho">
        <div id="cabi">
            <img src="tampinha2.png" width="100" height="100" alt="PRI logo">
        </div>
        <div id="cabt">
            <h1>Relatórios de Coletas</h1>
            <h4> Usuário Logdo: <?php echo $_SESSION['user_nome'];?></h4>
            <h4> Perfil: <?php echo $_SESSION['user_perfil'];?></h4>
            <h4> Pontos: <?php echo $_SESSION['user_pontos'];?></h4>
            <a href="#" class="btn" onclick="window.location.reload();">Atualizar Dados</a>
            <button class="btn" type="button" onclick="window.location.href='inicio.php'">Voltar</button>
            <button class="btn" title="exporta sem filtors para .txt"type="submit" name="tabela" value="coletas">Exportar Relatório</button>
            </form>
         </div>
    </div>
</header>
<?php
// Verifique se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    die("Você precisa estar logado para acessar essa página.");
}
$user_perfil = $_SESSION['user_perfil'];
$user_id = $_SESSION['user_id']; // O ID do usuário logado
// Variáveis para os filtros
$nome_filter = isset($_GET['nome']) ? $_GET['nome'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$inicio_filter = isset($_GET['inicio']) ? $_GET['inicio'] : '';
$fim_filter = isset($_GET['fim']) ? $_GET['fim'] : '';

// Conexão com o banco de dados
$dsn = 'mysql:host=localhost;dbname=inodb';
$username = 'root';
$password = 'xxxxxxxxxxx';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL para buscar as coletas, incluindo os nomes de provedor e coletor
    $sql = "SELECT c.id, p.nome AS provedor, u.nome AS coletor, c.endereco,
        c.data_provisionamento, c.periodo_inicio, c.periodo_fim, c.status, c.data_reserva, c.data_coleta
        FROM coletas c
        LEFT JOIN users p ON c.provedor_id = p.id
        LEFT JOIN users u ON c.coletor_id = u.id
        WHERE (c.provedor_id = :user_id OR c.coletor_id = :user_id)";


 // Adicionando filtros se fornecidos
    if ($nome_filter) {
       if($user_perfil == 'provedor') {
                 $sql .= "AND   u.nome LIKE :nome";
         }

       if($user_perfil == 'coletor')  {
                   $sql .= "AND  p.nome LIKE :nome";
         }
    }

    if ($status_filter) {
        $sql .= " AND c.status = :status";
    }

    if ($inicio_filter) {
        $sql .= " AND c.data_provisionamento >= :inicio";
    }

    if ($fim_filter) {
        $sql .= " AND c.data_provisionamento <= :fim";
    }

    // Preparar a consulta
    $stmt = $pdo->prepare($sql);

    // Passar os parâmetros
    $params = [':user_id' => $user_id];

    if ($nome_filter) {
        $params[':nome'] = "%$nome_filter%";
    }
    if ($status_filter) {
        $params[':status'] = $status_filter;

    }
    if ($inicio_filter) {
        $params[':inicio'] = $inicio_filter;
    }
    if ($fim_filter) {
        $params[':fim'] = $fim_filter;
    }

    // Executar a consulta
    $stmt->execute($params);


    // Exibindo o formulário de filtros
    echo "<form method='GET'>
           <div class='filtro'>
             <div>
              <h2> Filtro de pesquisa</h2>
             </div>
             <div id='fstatus'>
                   <label for='status'>Status:</label>
                   <select name='status'>
                   <option value=''>Todos</option>
                   <option value='disponivel' " . ($status_filter == 'disponivel' ? 'selected' : '') . ">Disponível</option>
                   <option value='reservada' " . ($status_filter == 'reservada' ? 'selected' : '') . ">Reservada</option>
                   <option value='coletado' " . ($status_filter == 'coletado' ? 'selected' : '') . ">Coletado</option>
                   <option value='excluído' " . ($status_filter == 'excluído' ? 'selected' : '') . ">Excluído</option>
               </select>
            </div>
            <div id='fnome'>
            <label for='nome'>Nome:</label>
            <input type='text' name='nome' value='$nome_filter'>
            </div>
            <div id='finicio'>
            <label for='inicio'>Data Início:</label>
            <input type='date' name='inicio' value='$inicio_filter'>
            </div>
            <div id='ffim'>
            <label for='fim'>Data Fim:</label>
            <input type='date' name='fim' value='$fim_filter'>
            </div>
            <div id='fsubmit'>
            <input type='submit' value='Filtrar'>
            </div>  
          </div>
          </form>";

    // Exibindo os resultados em uma tabela HTML
    echo "<table border='1'>
            <thead>
                <tr>";
        if ($user_perfil == 'coletor') {
             echo "<th>Nome do Solicitante</th>";
           } else {
             echo "<th>Nome do Coletor</th>";
           }
   
        if ($user_perfil == 'coletor') {
           echo "<th>Endereço</th>";
           }
            echo "<th>Cadastrado Em</th>
                    <th>Período Início</th>
                    <th>Período Fim</th>
                    <th>Status</th>
                    <th>Data Reserva</th>
                    <th>Data Coleta/Exclusão</th>
                </tr>
            </thead>
            <tbody>";

    // Loop para exibir cada linha de resultado
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        if ($user_perfil == 'coletor') {
        echo "<td>" . htmlspecialchars($row['provedor']) . "</td>";
        } else {
        echo "<td>" . htmlspecialchars($row['coletor'] ?? 'Sem Coletor') . "</td>";
        }
        if ($user_perfil == 'coletor') {
        echo "<td>" . htmlspecialchars($row['endereco']) . "</td>";
        }
        echo "<td>" . htmlspecialchars($row['data_provisionamento']) . "</td>";
        echo "<td>" . htmlspecialchars($row['periodo_inicio']) . "</td>";
        echo "<td>" . htmlspecialchars($row['periodo_fim']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>" . htmlspecialchars($row['data_reserva']) . "</td>";
        echo "<td>" . htmlspecialchars($row['data_coleta']) . "</td>";
        echo "</tr>";
    }

    echo "</tbody>
        </table>";

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}


?>
