<?php
// Conectar ao banco de dados
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

// Pegando os filtros da URL
$status = isset($_GET['status']) ? $_GET['status'] : '';
$provedor_id = isset($_GET['provedor_id']) ? $_GET['provedor_id'] : '';
$coletor_id = isset($_GET['coletor_id']) ? $_GET['coletor_id'] : '';
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

// Base da consulta
$sql = "SELECT c.*, 
               u1.nome AS provedor_nome, 
               u1.cpf AS provedor_cpf, 
               u1.email AS provedor_email,
               u2.nome AS coletor_nome, 
               u2.cpf AS coletor_cpf, 
               u2.email AS coletor_email
        FROM coletas c
        LEFT JOIN users u1 ON c.provedor_id = u1.id
        LEFT JOIN users u2 ON c.coletor_id = u2.id
        WHERE 1";  // WHERE 1 é sempre verdadeiro, para facilitar a adição de condições

// Adicionando filtros conforme os parâmetros
if ($status) {
    $sql .= " AND c.status LIKE :status";
}
if ($provedor_id) {
    $sql .= " AND c.provedor_id = :provedor_id";
}
if ($coletor_id) {
    $sql .= " AND c.coletor_id = :coletor_id";
}
if ($data_inicio) {
    $sql .= " AND c.data_coleta >= :data_inicio";
}
if ($data_fim) {
    $sql .= " AND c.data_coleta <= :data_fim";
}

// Preparando a consulta
$stmt = $pdo->prepare($sql);

// Atribuindo os parâmetros
if ($status) {
    $stmt->bindValue(':status', "%$status%");
}
if ($provedor_id) {
    $stmt->bindValue(':provedor_id', $provedor_id);
}
if ($coletor_id) {
    $stmt->bindValue(':coletor_id', $coletor_id);
}
if ($data_inicio) {
    $stmt->bindValue(':data_inicio', $data_inicio);
}
if ($data_fim) {
    $stmt->bindValue(':data_fim', $data_fim);
}

// Executando a consulta
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Coletas</title>
    <link rel="stylesheet" href="rel.css"> <!-- Se quiser um arquivo CSS separado -->
</head>
<body>

    <h1>Relatório de Coletas</h1>

    <form method="GET" action="rel.php">
        <label for="status">Status:</label>
        <input type="text" name="status" id="status" placeholder="Status da coleta" value="<?= htmlspecialchars($status) ?>">

        <label for="provedor_id">Provedor:</label>
        <select name="provedor_id" id="provedor_id">
            <option value="">Selecione</option>
            <?php
            // Preenchendo o dropdown com os provedores
            $stmt = $pdo->query("SELECT id, nome FROM users WHERE perfil = 'provedor'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = ($row['id'] == $provedor_id) ? 'selected' : '';
                echo "<option value='{$row['id']}' $selected>{$row['nome']}</option>";
            }
            ?>
        </select>

        <label for="coletor_id">Coletor:</label>
        <select name="coletor_id" id="coletor_id">
            <option value="">Selecione</option>
            <?php
            // Preenchendo o dropdown com os coletores
            $stmt = $pdo->query("SELECT id, nome FROM users WHERE perfil = 'coletor'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = ($row['id'] == $coletor_id) ? 'selected' : '';
                echo "<option value='{$row['id']}' $selected>{$row['nome']}</option>";
            }
            ?>
        </select>

        <label for="data_inicio">Data de Início:</label>
        <input type="date" name="data_inicio" id="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>">

        <label for="data_fim">Data de Fim:</label>
        <input type="date" name="data_fim" id="data_fim" value="<?= htmlspecialchars($data_fim) ?>">

        <input type="submit" value="Filtrar">
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Provedor Nome</th>
                <th>Provedor CPF</th>
                <th>Provedor Email</th>
                <th>Coletor Nome</th>
                <th>Coletor CPF</th>
                <th>Coletor Email</th>
                <th>Data Coleta</th>
                <th>Status</th>
                <th>Endereço</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['provedor_nome'] ?></td>
                    <td><?= $row['provedor_cpf'] ?></td>
                    <td><?= $row['provedor_email'] ?></td>
                    <td><?= $row['coletor_nome'] ?></td>
                    <td><?= $row['coletor_cpf'] ?></td>
                    <td><?= $row['coletor_email'] ?></td>
                    <td><?= $row['data_coleta'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['endereco'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>

