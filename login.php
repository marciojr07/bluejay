<?php
session_start();
include_once("conexao2.php"); // Certifique-se de que este caminho está correto
var_dump($conexao);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se os campos estão preenchidos
    if (empty($_POST['username']) || empty($_POST['password'])) {
        echo "Por favor, preencha todos os campos.";
        exit();
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Prepare a consulta para buscar o usuário
        $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug: Verifique o que foi retornado
        var_dump($user); // Isso deve mostrar os dados do usuário

        // Verifica se o usuário existe e se a senha está correta
        if ($user && $user['password'] === $password) { // Comparação direta, pois a senha está em texto simples
            // Armazena o ID do usuário na sessão
            $_SESSION['user_id'] = $user['id'];
            header("Location: ../dashboard.html"); // Redireciona para o dashboard
            exit();
        } else {
            echo "Usuário ou senha incorretos.";
        }
    } catch (PDOException $e) {
        // Trate o erro de conexão ou consulta
        echo "Erro ao acessar o banco de dados: " . $e->getMessage();
    }
}
?>