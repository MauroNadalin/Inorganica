<?php
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






?>
