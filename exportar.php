<?php

include('conexao.php');
include('abre_ou_redireciona.php');// Conectar ao banco de dados

$host = 'localhost';
$dbname = 'inodb';
$username = 'root';
$password = 'xxxxxxxxxxx';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}


$user_id = $_SESSION['user_id'];
$dH = date('YmdHis');
$arquivo = 'coletas' . $user_id . '_' . $dH . '.txt';

function exportarCSV($tabela, $arquivo, $user_id) {
    global $pdo;

    // Consultar os dados da tabela
    $sql =  "SELECT c.id, p.nome AS provedor, u.nome AS coletor, c.endereco,
        c.data_provisionamento, c.periodo_inicio, c.periodo_fim, c.status, c.data_reserva, c.data_coleta
        FROM $tabela c
        LEFT JOIN users p ON c.provedor_id = p.id
        LEFT JOIN users u ON c.coletor_id = u.id
        WHERE c.provedor_id = :user_id OR c.coletor_id = :user_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);

    // Definir cabeçalhos para download
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=$arquivo");

    // Abrir o arquivo PHP para saída
    $output = fopen('php://output', 'w');

    // Adicionar os nomes das colunas no arquivo CSV
    $colunas = array_keys($stmt->fetch(PDO::FETCH_ASSOC)); // Pega os nomes das colunas
    fputcsv($output, $colunas); // Escreve as colunas no CSV

    // Adicionar os dados das tabelas no CSV
        $pdo->exec("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");

    $stmt->execute(); // Reexecuta a consulta para obter todos os dados
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row); // Escreve as linhas de dados no CSV
    }

    // Fechar o arquivo após a exportação
    fclose($output);
    exit();
}
//teste chamar a função direto:
//        exportarCSV('coletas', 'coletas.csv', $user_id);

// Verificar se a tabela foi passada na URL
if (isset($_GET['tabela'])) {
    $tabela = $_GET['tabela'];
    if ($tabela == 'coletas') {
        exportarCSV('coletas', $arquivo, $user_id);
    } elseif ($tabela == 'users') {
        exportarCSV('users', 'users.csv', $user_id);
    }
}
?>

