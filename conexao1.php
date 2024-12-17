<?php

$host = "localhost"; // Definindo o host
$usuario = "root"; // Seu nome de usuário do banco de dados
$senha = ""; // Sua senha do banco de dados
$dbname = "pizzaria"; // Nome do banco de dados

try {
    // Criando a conexão com o banco de dados
    $conexao = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $senha);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Configurando o modo de erro
} catch (PDOException $e) {
    // Tratando erros de conexão
    echo "Erro de conexão: " . $e->getMessage();
    exit(); // Encerra o script se a conexão falhar
}
?>