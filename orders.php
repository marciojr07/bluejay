<?php
include_once("conexao1.php");

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    $pedidosQuery = $conexao->query("SELECT * FROM pedidos;");
    $pedidos = $pedidosQuery->fetchAll(PDO::FETCH_ASSOC);

    $pizzas = [];

    foreach ($pedidos as $pedido) {
        $pizza = [];
        $pizza["id"] = $pedido["pizza_id"];

        $pizzaQuery = $conexao->prepare("SELECT * FROM pizzas WHERE id = :pizza_id");
        $pizzaQuery->bindParam(":pizza_id", $pizza["id"]);
        $pizzaQuery->execute();

        $pizzaData = $pizzaQuery->fetch(PDO::FETCH_ASSOC);

        $bordaQuery = $conexao->prepare("SELECT * FROM bordas WHERE id = :borda_id");
        $bordaQuery->bindParam(":borda_id", $pizzaData["borda_id"]);
        $bordaQuery->execute();

        $borda = $bordaQuery->fetch(PDO::FETCH_ASSOC);
        $pizza["borda"] = $borda["tipo"];

        $massaQuery = $conexao->prepare("SELECT * FROM massas WHERE id = :massa_id");
        $massaQuery->bindParam(":massa_id", $pizzaData["massa_id"]);
        $massaQuery->execute();

        $massa = $massaQuery->fetch(PDO::FETCH_ASSOC);
        $pizza["massa"] = $massa["tipo"];

        $saboresQuery = $conexao->prepare("SELECT * FROM pizzas_sabor WHERE pizza_id = :pizza_id");
        $saboresQuery->bindParam(":pizza_id", $pizza["id"]);
        $saboresQuery->execute();

        $sabores = $saboresQuery->fetchAll(PDO::FETCH_ASSOC);

        $saboresDataPizza = [];
        $saborQuery = $conexao->prepare("SELECT * FROM sabores WHERE id = :sabor_id");

        foreach ($sabores as $sabor) {
            $saborQuery->bindParam(":sabor_id", $sabor["sabor_id"]);
            $saborQuery->execute();

            $saborPizza = $saborQuery->fetch(PDO::FETCH_ASSOC);
            array_push($saboresDataPizza, $saborPizza["nome"]);
        }

        $pizza["sabores"] = $saboresDataPizza;
        $pizza["status"] = $pedido["status_id"];
        array_push($pizzas, $pizza);
    }

    echo json_encode($pizzas);
}

if ($method === "POST") {
    $type = $_POST["type"];

    if ($type === "update") {
        $pizzaId = $_POST["pizza_id"];
        $statusId = $_POST["status_id"];

        $updateQuery = $conexao->prepare("UPDATE pedidos SET status_id = :status_id WHERE pizza_id = :pizza_id");
        $updateQuery->bindParam(":pizza_id", $pizzaId, PDO::PARAM_INT);
        $updateQuery->bindParam(":status_id", $statusId, PDO::PARAM_INT);
        $updateQuery->execute();

        echo json_encode(["success" => true, "message" => "Status atualizado com sucesso!"]);
        exit();
    }
}

if ($method === "DELETE") {
    parse_str(file_get_contents("php://input"), $data);
    $pizzaId = $data["pizza_id"];

    $deleteQuery = $conexao->prepare("DELETE FROM pedidos WHERE pizza_id = :pizza_id");
    $deleteQuery->bindParam(":pizza_id", $pizzaId, PDO::PARAM_INT);

    if ($deleteQuery->execute()) {
        echo json_encode(["success" => true, "message" => "Pedido excluído com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao excluir o pedido."]);
    }
    exit();
}

if ($method === "PUT") {
    parse_str(file_get_contents("php://input"), $data);
    $pizzaId = $data["pizza_id"];

    header("Location: process/atual.html?pizza_id=$pizzaId");
    exit();
}
?>
