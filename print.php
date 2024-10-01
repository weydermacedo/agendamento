<?php
require_once "config.php";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$conn = getConnection();

// Função para obter o agendamento pelo ID
function getAgendamentoById($conn, $id) {
    $sql = "SELECT * FROM agendamentos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

$agendamento = getAgendamentoById($conn, $id);
if (!$agendamento) {
    die("Agendamento não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendamento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .comanda {
            width: 300px;
            margin: 0 auto;
            text-align: center;
        }
        .comanda h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .comanda p {
            font-size: 18px;
            margin: 5px 0;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="comanda">
        <h1>Agendamento</h1>
        <p><strong>ID:</strong> <?php echo $agendamento['id']; ?></p>
        <p><strong>Nome:</strong> <?php echo $agendamento['nome']; ?></p>
        <p><strong>Telefone:</strong> <?php echo $agendamento['telefone']; ?></p>
        <p><strong>Data e Hora:</strong> <?php echo date('d-m-Y H:i', strtotime($agendamento['data_hora'])); ?></p>
        <p><strong>Serviço:</strong> <?php echo $agendamento['servico']; ?></p>
        <p><strong>Atendente:</strong> <?php echo $agendamento['atendente']; ?></p>
    </div>
</body>
</html>