<?php
session_start(); // Inicia a sessão
include_once("conexao1.php"); // Incluindo o arquivo de conexão

$method = $_SERVER["REQUEST_METHOD"];

try {
    // Resgate dos dados, montagem do pedido
    if ($method === "GET") {
        $bordasQuery = $conexao->query("SELECT * FROM bordas;");
        $bordas = $bordasQuery->fetchAll(PDO::FETCH_ASSOC);

        $massasQuery = $conexao->query("SELECT * FROM massas;");
        $massas = $massasQuery->fetchAll(PDO::FETCH_ASSOC);

        $saboresQuery = $conexao->query("SELECT * FROM sabores;");
        $sabores = $saboresQuery->fetchAll(PDO::FETCH_ASSOC);
    } else if ($method === "POST") {
        // Verificação se os dados estão definidos
        if (!isset($_POST["borda"], $_POST["massa"], $_POST["sabores"]) || 
            !is_array($_POST["sabores"])) {
            $_SESSION["msg"] = "Erro: Dados inválidos.";
            $_SESSION["status"] = "error";
            header("Location: ..");
            exit;
        }

        $borda = $_POST["borda"];
        $massa = $_POST["massa"];
        $sabores = $_POST["sabores"];

        // Validação de sabores máximos
        if (count($sabores) > 3) {
            $_SESSION["msg"] = "Selecione no máximo 3 sabores!";
            $_SESSION["status"] = "warning";
            header("Location: .."); // Redireciona para a página anterior
            exit;
        } else {
            // Salvando borda e massa na pizza
            $sqli = $conexao->prepare("INSERT INTO pizzas (borda_id, massa_id) VALUES (:borda, :massa)");
            $sqli->bindParam(":borda", $borda, PDO::PARAM_INT);
            $sqli->bindParam(":massa", $massa, PDO::PARAM_INT);
            $sqli->execute();

            // Resgatando o último id da última pizza
            $pizzaId = $conexao->lastInsertId();

            // Inserindo sabores na pizza
            $sqli = $conexao->prepare("INSERT INTO pizzas_sabor (pizza_id, sabor_id) VALUES (:pizza, :sabor)");
            foreach ($sabores as $sabor) {
                $sqli->bindParam(":pizza", $pizzaId, PDO::PARAM_INT);
                $sqli->bindParam(":sabor", $sabor, PDO::PARAM_INT);
                $sqli->execute();
            }

            // Criar o pedido da pizza
            $sqli = $conexao->prepare("INSERT INTO pedidos (pizza_id, status_id) VALUES (:pizza, :status)");
            $statusId = 1; // Supondo que 1 é o status "Em preparação"
            $sqli->bindParam(":pizza", $pizzaId, PDO::PARAM_INT);
            $sqli->bindParam(":status", $statusId, PDO::PARAM_INT);
            $sqli->execute();

            // Mensagem de sucesso
            $_SESSION["msg"] = "Pedido realizado com sucesso!";
            $_SESSION["status"] = "success";

            // Redireciona para a página de sucesso
            header("Location: http://localhost/PIZZARIA.COM11/sucesso.html");
            exit;
        }
    }
} catch (PDOException $e) {
    // Tratando erros de execução
    $_SESSION["msg"] = "Erro de banco de dados: " . $e->getMessage();
    $_SESSION["status"] = "error";
    header("Location: index.html"); // Redireciona para a página anterior
    exit();
} catch (Exception $e) {
    // Tratando erros gerais
    $_SESSION["msg"] = "Erro: " . $e->getMessage();
    $_SESSION["status"] = "error";
    header("Location: index.html"); // Redireciona para a página anterior
    exit();
} finally {
    // Fecha a conexão (sempre é executado, independente de erro ou não)
    if (isset($conexao)) {
        $conexao = null;
    }
}
?>
