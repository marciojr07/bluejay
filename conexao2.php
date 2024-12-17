<?php
$host = 'localhost'; // ou o endereço do seu servidor de banco de dados
$dbname = 'gerencer_steve'; // substitua pelo nome do seu banco de dados
$username = 'root'; // substitua pelo seu usuário do banco de dados
$password = ''; // substitua pela sua senha do banco de dados

try {
    $conexao = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
?>