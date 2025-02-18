<?php
//include('config.php');
//session_start(); // ja iniciada no login
//include('config.php'); //define as variaveis tipo objeto bd ($pdo)
include('conexao.php'); // Aqui deve estar a sua conexão com o banco de dados
include('abre_ou_redireciona.php');

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inorganica - Cadastro de coletas</title>
    <link rel="stylesheet" href="coletaprov.css">
</head>
<body>

<header>
    <div class="cabecalho">
        <div id="cabi">
            <img src="tampinha2.png" width="100" height="100" alt="PRI logo">
        </div>
        <div id="cabt">
            <h1>Cadastro de Coletas</h1>
                <h4> Usuário Logdo: <?php echo $_SESSION['user_nome'];?></h4>
                <h4> Perfil: <?php echo $_SESSION['user_perfil'];?></h4>
                <h4> Pontos: <?php echo $_SESSION['user_pontos'];?></h4>
            <a href="#" class="btn" onclick="window.location.reload();">Atualizar Dados</a>
            <button class="btn" type="button" onclick="window.location.href='inicio.php'">Voltar</button>
        </div>
    </div>
</header>

<?php
// Recupera o ID e o endereço do provedor da sessão
$provedor_id = $_SESSION['user_id']; // ID do provedor logado
$query = "SELECT rua, numero, bairro, cidade, estado FROM users WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$provedor_id]);
$endereco = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados do formulário
    $data_provisionamento = $_POST['data_provisionamento'];
    $periodo_inicio = $_POST['periodo_inicio'];
    $periodo_fim = $_POST['periodo_fim'];

    // Insere a coleta no banco de dados
    $query = "INSERT INTO coletas (provedor_id, data_provisionamento, periodo_inicio, periodo_fim, endereco, status) 
              VALUES (?, ?, ?, ?, ?, 'disponivel')";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        $provedor_id, 
        $data_provisionamento, 
        $periodo_inicio, 
        $periodo_fim,
        $endereco['rua'] . ', ' . $endereco['numero'] . ', ' . $endereco['bairro'] . ', ' . $endereco['cidade'] . ', ' . $endereco['estado']
    ]);
    echo "Coleta cadastrada com sucesso!";

    // Após o sucesso da inserção, redireciona para a mesma página
    header("Location: " . $_SERVER['PHP_SELF']);
    exit(); // Evita que o código após o redirecionamento seja executado
}

// Lógica para atualizar o status para 'excluído'
if (isset($_GET['cancelar'])) {
    $coleta_id = $_GET['cancelar'];
    $query = "UPDATE coletas SET status = 'excluído', data_coleta = NOW()  WHERE id = ? AND provedor_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$coleta_id, $provedor_id]);
    echo "Status da coleta atualizado para 'excluído' com sucesso!";
    // Redireciona após a atualização para evitar re-execução do código ao atualizar
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<form method="POST">
    <h2>Cadastrar Coleta</h2>
    <input type="hidden" name="provedor_id" value="<?php echo $provedor_id; ?>">
    
    <label for="data_provisionamento">Data de Provisionamento:</label>
    <input type="datetime-local" name="data_provisionamento" required><br>

    <label for="periodo_inicio">Período da Coleta (Início):</label>
    <input type="datetime-local" name="periodo_inicio" required><br>

    <label for="periodo_fim">Período da Coleta (Fim):</label>
    <input type="datetime-local" name="periodo_fim" required><br>

    <label>Endereço:</label>
    <p><?php echo $endereco['rua'] . ', ' . $endereco['numero'] . ', ' . $endereco['bairro'] . ', ' . $endereco['cidade'] . ', ' . $endereco['estado']; ?></p>

    <button type="submit">Cadastrar Coleta</button>
</form>
<section>
  <div class="resumo">
    <h3>Coletas em Tratamento</h3>
<?php
 $query = "
        SELECT c.id, p.nome AS provedor, u.nome AS coletor, c.endereco,
               c.data_provisionamento, c.periodo_inicio, c.periodo_fim, c.status
        FROM coletas c
        LEFT JOIN users p ON c.provedor_id = p.id
        LEFT JOIN users u ON c.coletor_id = u.id
        WHERE (c.provedor_id = $provedor_id)
        AND (c.status != 'coletado')
        AND (c.status != 'excluido')";







$stmt = $pdo->prepare($query);
$stmt->execute([$provedor_id]);
$coletas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($coletas) > 0) {
    echo "<table><tr><th>Data</th><th>Coletor</th><th>Período</th><th>Status</th><th>Cancelar</th></tr>";
    foreach ($coletas as $coleta) {
        echo "<tr><td>{$coleta['data_provisionamento']}</td><td>{$coleta['coletor']}</td><td>{$coleta['periodo_inicio']} - {$coleta['periodo_fim']}</td><td>{$coleta['status']}</td>";
        echo "<td><a href='?cancelar={$coleta['id']}' class='btn'>Cancelar</a></td></tr>";
    }
    echo "</table>";
} else {
    echo "Nenhuma coleta em tratamento.";
}
?>
</div>
</section>
</body>
</html>

