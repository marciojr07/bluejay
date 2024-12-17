<?php
session_start(); // Inicia a sessão

$host = "localhost"; // Definindo o host
$usuario = "root"; // Seu nome de usuário do banco de dados
$senha = ""; // Sua senha do banco de dados
$dbname = "cadastro_steve"; // Nome do banco de dados

try {
    // Criando a conexão com o banco de dados
    $conexao = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $senha);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Configurando o modo de erro
} catch (PDOException $e) {
    // Tratando erros de conexão
    echo "Erro de conexão: " . $e->getMessage();
    exit(); // Encerra o script se a conexão falhar
}

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificando se os campos estão definidos
    if (isset($_POST['codigo'], $_POST['nome'], $_POST['endereco'], $_POST['telefone'])) {
        $codigo = $_POST['codigo'];
        $nome = $_POST['nome'];
        $endereco = $_POST['endereco'];
        $telefone = $_POST['telefone'];

        // Preparando a consulta SQL
        $sql = "INSERT INTO cliente (codigo, nome, endereco, telefone) VALUES (:codigo, :nome, :endereco, :telefone)";
        
        try {
            $stmt = $conexao->prepare($sql);
            
            // Bind dos parâmetros corretamente
            $stmt->bindParam(':codigo', $codigo);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':telefone', $telefone);
            
            // Executando a consulta
            $stmt->execute();

            // Mensagem de sucesso
            $_SESSION["msg"] = "Cadastro realizado com sucesso!";
            $_SESSION["status"] = "success";
       
            // Redireciona para a página de sucesso
            header("Location: http://localhost/PIZZARIA.COM11/sucesso1.html");
            exit;
        } catch (PDOException $e) {
            // Tratando erros de execução
            $_SESSION["msg"] = "Erro ao realizar o cadastro: " . $e->getMessage();
            $_SESSION["status"] = "error";
            header("Location: http://localhost/PIZZARIA.COM11/cadastro.html"); // Redireciona de volta ao formulário
            exit;
        }
    } else {
        // Se os campos não estiverem definidos
        $_SESSION["msg"] = "Por favor, preencha todos os campos.";
        $_SESSION["status"] = "warning";
        header("Location: http://localhost/PIZZARIA.COM11/cadastro.html"); // Redireciona de volta ao formulário
        exit;
    }
}

// Fecha a conexão
if (isset($conexao)) {
    $conexao = null;
}
?>