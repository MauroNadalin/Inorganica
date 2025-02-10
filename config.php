<?php
session_start(); // Inicia a sessão para verificar se o usuário está logado

// Configuração do banco de dados
$servername = "localhost";
$username = "root";
$password = "mAu52gro$$0";
$dbname = "inodb";

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Função para verificar se o usuário está logado
function usuarioLogado() {
    return isset($_SESSION['user_id']);
}
?>
