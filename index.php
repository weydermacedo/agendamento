<?php
session_start();
require_once "config.php";
require('login/session.php');
$conn = getConnection();
function getEvolutionAPIConfigs($conn) {
    $sql = "SELECT * FROM evolution_api_config";
    return $conn->query($sql);
}
function verificaHorarioDisponivel($conn, $data_hora) {
    $sql = "SELECT * FROM agendamentos WHERE data_hora = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $data_hora);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows == 0;
}
function verificaHorarioDisponibilidade($conn, $horario) {
    $sql = "SELECT disponivel FROM horarios WHERE horario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $horario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['disponivel'] == 1;
    }
    return false;
}
function getServicos($conn) {
    $sql = "SELECT * FROM servicos";
    return $conn->query($sql);
}
function getHorarios($conn) {
    $sql = "SELECT * FROM horarios";
    return $conn->query($sql);
}
function getAtendenteServicos($conn) {
    $sql = "SELECT aserv.id, atendentes.nome as atendente, servicos.nome as servico
            FROM atendente_servicos aserv
            JOIN atendentes ON aserv.atendente_id = atendentes.id
            JOIN servicos ON aserv.servico_id = servicos.id";
    return $conn->query($sql);
}
$data_selecionada = "";
if (isset($_POST["data"])) {
    $data_selecionada = $_POST["data"];
}
$horario_selecionado = "";
if (isset($_POST["horario"])) {
    $horario_selecionado = $_POST["horario"];
}
if (isset($_POST["agendar"])) {
    $nome = $_POST["nome"];
    $telefone = $_POST["telefone"];
    $servico = $_POST["servico"];
    $atendente = $_POST["atendente"];
    $data_hora = date('d-m-Y', strtotime($data_selecionada)) . " " . $horario_selecionado;
    if (verificaHorarioDisponivel($conn, $data_hora) && verificaHorarioDisponibilidade($conn, $horario_selecionado)) {
        $sql = "INSERT INTO agendamentos (nome, telefone, data_hora, servico, atendente) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nome, $telefone, $data_hora, $servico, $atendente);
        if ($stmt->execute()) {
            $_SESSION['msg'] = 'Agendamento realizado com sucesso!';
            $_SESSION['msg_type'] = 'success';
        } else {
            $_SESSION['msg'] = 'Erro ao realizar o agendamento: ' . $stmt->error;
            $_SESSION['msg_type'] = 'error';
        }
    } else {
        $_SESSION['msg'] = 'Horário já está ocupado ou indisponível.';
        $_SESSION['msg_type'] = 'error';
    }
    $_SESSION['active_tab'] = 'agendamentos';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
function getAgendamentos($conn) {
    $sql = "SELECT * FROM agendamentos";
    return $conn->query($sql);
}
function deleteAgendamento($conn, $id) {
    $sql = "DELETE FROM agendamentos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
function getAgendamentoById($conn, $id) {
    $sql = "SELECT * FROM agendamentos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
function updateAgendamento($conn, $id, $nome, $telefone, $data_hora, $servico, $atendente) {
    $sql = "UPDATE agendamentos SET nome = ?, telefone = ?, data_hora = ?, servico = ?, atendente = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nome, $telefone, $data_hora, $servico, $atendente, $id);
    return $stmt->execute();
}
function getServicosList($conn) {
    $sql = "SELECT * FROM servicos";
    return $conn->query($sql);
}
function addServico($conn, $nome) {
    $sql = "INSERT INTO servicos (nome) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nome);
    return $stmt->execute();
}
function updateServico($conn, $id, $nome) {
    $sql = "UPDATE servicos SET nome = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nome, $id);
    return $stmt->execute();
}
function deleteServico($conn, $id) {
    $sql = "DELETE FROM servicos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
function getHorariosList($conn) {
    $sql = "SELECT * FROM horarios";
    return $conn->query($sql);
}
function addHorario($conn, $horario) {
    $sql = "INSERT INTO horarios (horario) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $horario);
    return $stmt->execute();
}
function updateHorario($conn, $id, $horario) {
    $sql = "UPDATE horarios SET horario = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $horario, $id);
    return $stmt->execute();
}
function deleteHorario($conn, $id) {
    $sql = "DELETE FROM horarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
function setHorarioDisponibilidade($conn, $id, $disponivel) {
    $sql = "UPDATE horarios SET disponivel = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $disponivel, $id);
    return $stmt->execute();
}
function getAtendentes($conn) {
    $sql = "SELECT * FROM atendentes";
    return $conn->query($sql);
}
function addAtendente($conn, $nome, $cargo, $disponivel) {
    $sql = "INSERT INTO atendentes (nome, cargo, disponivel) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nome, $cargo, $disponivel);
    return $stmt->execute();
}
function updateAtendente($conn, $id, $nome, $cargo, $disponivel) {
    $sql = "UPDATE atendentes SET nome = ?, cargo = ?, disponivel = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $nome, $cargo, $disponivel, $id);
    return $stmt->execute();
}
function deleteAtendente($conn, $id) {
    $sql = "DELETE FROM atendentes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
function setAtendenteDisponibilidade($conn, $id, $disponivel) {
    $sql = "UPDATE atendentes SET disponivel = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $disponivel, $id);
    return $stmt->execute();
}
function getUsers($conn) {
    $sql = "SELECT * FROM admin_log";
    return $conn->query($sql);
}
function addUser($conn, $username, $password) {
    $hashed_password = md5($password); 
    $sql = "INSERT INTO admin_log (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $hashed_password);
    return $stmt->execute();
}
function updateUser($conn, $id, $username, $password) {
    $hashed_password = md5($password); 
    $sql = "UPDATE admin_log SET username = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $hashed_password, $id);
    return $stmt->execute();
}
function deleteUser($conn, $id) {
    $sql = "DELETE FROM admin_log WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
function addAtendenteServico($conn, $atendente_id, $servico_id) {
    $sql = "INSERT INTO atendente_servicos (atendente_id, servico_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $atendente_id, $servico_id);
    return $stmt->execute();
}
function updateAtendenteServico($conn, $id, $atendente_id, $servico_id) {
    $sql = "UPDATE atendente_servicos SET atendente_id = ?, servico_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $atendente_id, $servico_id, $id);
    return $stmt->execute();
}
function deleteAtendenteServico($conn, $id) {
    $sql = "DELETE FROM atendente_servicos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
if (isset($_POST["action"]) && $_POST["action"] == "addAgendamento") {
    $nome = $_POST["nome"];
    $telefone = $_POST["telefone"];
    $data = $_POST["data"];
    $horario = $_POST["horario"];
    $servico = $_POST["servico"];
    $atendente = $_POST["atendente"];
    $data_hora = date('d-m-Y', strtotime($data)) . " " . $horario;
    if (verificaHorarioDisponivel($conn, $data_hora) && verificaHorarioDisponibilidade($conn, $horario)) {
        $sql = "INSERT INTO agendamentos (nome, telefone, data_hora, servico, atendente) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nome, $telefone, $data_hora, $servico, $atendente);
        if ($stmt->execute()) {
            $_SESSION['msg'] = 'Agendamento cadastrado com sucesso!';
            $_SESSION['msg_type'] = 'success';
        } else {
            $_SESSION['msg'] = 'Erro ao cadastrar o agendamento: ' . $stmt->error;
            $_SESSION['msg_type'] = 'error';
        }
    } else {
        $_SESSION['msg'] = 'Horário já está ocupado ou indisponível.';
        $_SESSION['msg_type'] = 'error';
    }
    $_SESSION['active_tab'] = 'cadastro';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action == 'addServico') {
        $nome = $_POST['nome'];
        addServico($conn, $nome);
        $_SESSION['msg'] = 'Serviço adicionado com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'updateServico') {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        updateServico($conn, $id, $nome);
        $_SESSION['msg'] = 'Serviço atualizado com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'deleteServico') {
        $id = $_POST['id'];
        deleteServico($conn, $id);
        $_SESSION['msg'] = 'Serviço excluído com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'addHorario') {
        $horario = $_POST['horario'];
        addHorario($conn, $horario);
        $_SESSION['msg'] = 'Horário adicionado com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'updateHorario') {
        $id = $_POST['id'];
        $horario = $_POST['horario'];
        updateHorario($conn, $id, $horario);
        $_SESSION['msg'] = 'Horário atualizado com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'deleteHorario') {
        $id = $_POST['id'];
        deleteHorario($conn, $id);
        $_SESSION['msg'] = 'Horário excluído com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'setDisponivel') {
        $id = $_POST['id'];
        $disponivel = $_POST['disponivel'];
        setHorarioDisponibilidade($conn, $id, $disponivel);
        $_SESSION['msg'] = 'Disponibilidade do horário atualizada com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'deleteAgendamento') {
        $id = $_POST['id'];
        deleteAgendamento($conn, $id);
        $_SESSION['msg'] = 'Agendamento excluído com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'editAgendamento') {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $telefone = $_POST['telefone'];
        $data = $_POST['data'];
        $horario = $_POST['horario'];
        $data_hora = date('d-m-Y', strtotime($data)) . " " . $horario;
        $servico = $_POST['servico'];
        $atendente = $_POST['atendente'];
        if (verificaHorarioDisponivel($conn, $data_hora) || getAgendamentoById($conn, $id)['data_hora'] == $data_hora) {
            updateAgendamento($conn, $id, $nome, $telefone, $data_hora, $servico, $atendente);
            $_SESSION['msg'] = 'Agendamento atualizado com sucesso!';
            $_SESSION['msg_type'] = 'success';
        } else {
            $_SESSION['msg'] = 'Horário já está ocupado.';
            $_SESSION['msg_type'] = 'error';
        }
    } elseif ($action == 'addAtendente') {
        $nome = $_POST['nome'];
        $cargo = $_POST['cargo'];
        $disponivel = isset($_POST['disponivel']) ? 1 : 0;
        addAtendente($conn, $nome, $cargo, $disponivel);
        $_SESSION['msg'] = 'Atendente adicionado com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'updateAtendente') {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $cargo = $_POST['cargo'];
        $disponivel = isset($_POST['disponivel']) ? 1 : 0;
        updateAtendente($conn, $id, $nome, $cargo, $disponivel);
        $_SESSION['msg'] = 'Atendente atualizado com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'deleteAtendente') {
        $id = $_POST['id'];
        deleteAtendente($conn, $id);
        $_SESSION['msg'] = 'Atendente excluído com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'setAtendenteDisponibilidade') {
        $id = $_POST['id'];
        $disponivel = $_POST['disponivel'];
        setAtendenteDisponibilidade($conn, $id, $disponivel);
        $_SESSION['msg'] = 'Disponibilidade do atendente atualizada com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'addUser') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        addUser($conn, $username, $password);
        $_SESSION['msg'] = 'Usuário adicionado com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'updateUser') {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        updateUser($conn, $id, $username, $password);
        $_SESSION['msg'] = 'Usuário atualizado com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'deleteUser') {
        $id = $_POST['id'];
        deleteUser($conn, $id);
        $_SESSION['msg'] = 'Usuário excluído com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'addAtendenteServico') {
        $atendente_id = $_POST['atendente_id'];
        $servico_id = $_POST['servico_id'];
        addAtendenteServico($conn, $atendente_id, $servico_id);
        $_SESSION['msg'] = 'Serviço atribuído ao atendente com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'updateAtendenteServico') {
        $id = $_POST['id'];
        $atendente_id = $_POST['atendente_id'];
        $servico_id = $_POST['servico_id'];
        updateAtendenteServico($conn, $id, $atendente_id, $servico_id);
        $_SESSION['msg'] = 'Serviço atribuído ao atendente atualizado com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'deleteAtendenteServico') {
        $id = $_POST['id'];
        deleteAtendenteServico($conn, $id);
        $_SESSION['msg'] = 'Serviço atribuído ao atendente excluído com sucesso!';
        $_SESSION['msg_type'] = 'success';
    } elseif ($action == 'customizacao') {
        file_put_contents('customizacao.json', json_encode($_POST));
        $_SESSION['msg'] = 'Customização salva com sucesso!';
        $_SESSION['msg_type'] = 'success';
    }
    $_SESSION['active_tab'] = $_POST['active_tab'];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
$customizacao = json_decode(file_get_contents('customizacao.json'), true);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Agendamentos</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <style>
        :root {
            --primary-color: <?php echo isset($customizacao['primary_color']) ? $customizacao['primary_color'] : '#0042DA'; ?>;
        }
        .navbar {
            background-color: <?php echo isset($customizacao['navbar_color']) ? $customizacao['navbar_color'] : '#007BFF'; ?>;
        }
        .custom-navbar {
            background-color: <?php echo isset($customizacao['navbar_color']) ? $customizacao['navbar_color'] : '#007BFF'; ?>;
            padding: 10px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
<nav class="custom-navbar">
    <div class="navbar-content">
        <a class="navbar-nova" href="login/logout.php">
            Sair <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</nav>
<header class="header text-center">
    <div>
        <img src="<?php echo isset($customizacao['logo_url']) ? $customizacao['logo_url'] : 'https://i.postimg.cc/sg2BX9dg/logo.png'; ?>" alt="Logo" class="logo"><br>
    </div>
</header>
<div class="container">
    <ul class="nav nav-tabs flex-column flex-sm-row">
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#agendamentos" id="agendamentos-tab"><i class="fas fa-calendar-alt"></i> Agendamentos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#servicos" id="servicos-tab"><i class="fas fa-list"></i> Serviços</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#horarios" id="horarios-tab"><i class="fas fa-clock"></i> Horários</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#atendentes" id="atendentes-tab"><i class="fas fa-user"></i> Atendentes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#usuarios" id="usuarios-tab"><i class="fas fa-users"></i> Usuários</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#customizacao" id="customizacao-tab"><i class="fas fa-palette"></i> Customização</a>
        </li>
    </ul>
    <div class="tab-content mt-4">
        <div id="agendamentos" class="tab-pane fade">
            <?php
            $agendamentos = getAgendamentos($conn);
            ?>
            <h2>Lista de Agendamentos</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Data e Hora</th>
                        <th>Serviço</th>
                        <th>Atendente</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    while ($agendamento = $agendamentos->fetch_assoc()) {
                    $data_hora_formatada = date('d-m-Y H:i', strtotime($agendamento['data_hora']));
                        echo "<tr>";
                        echo "<td>" . $agendamento['id'] . "</td>";
                        echo "<td>" . $agendamento['nome'] . "</td>";
                        echo "<td>" . $agendamento['telefone'] . "</td>";
                        echo "<td>" . $data_hora_formatada . "</td>";
                        echo "<td>" . $agendamento['servico'] . "</td>";
                        echo "<td>" . $agendamento['atendente'] . "</td>";
                        echo "<td>
            <form method='post' style='display:inline-block' class='delete-form'>
                <input type='hidden' name='id' value='" . $agendamento['id'] . "'>
                <input type='hidden' name='action' value='deleteAgendamento'>
                <input type='hidden' name='active_tab' value='agendamentos'>
                <button type='button' class='btn btn-danger delete-btn'><i class='fas fa-trash-alt'></i> Excluir</button>
            </form>
            <button type='button' class='btn btn-primary edit-btn' data-id='".$agendamento['id']."' data-nome='".$agendamento['nome']."' data-telefone='".$agendamento['telefone']."' data-data='".date('Y-m-d', strtotime($agendamento['data_hora']))."' data-horario='".date('H:i', strtotime($agendamento['data_hora']))."' data-servico='".$agendamento['servico']."' data-atendente='".$agendamento['atendente']."'><i class='fas fa-edit'></i> Editar</button>
            <a href='print.php?id=" . $agendamento['id'] . "' target='_blank' class='btn btn-success'><i class='fas fa-print'></i> Imprimir</a>
          </td>";
                        echo "</tr>";
            }
                ?>
                </tbody>
            </table>
        </div>
        <div id="servicos" class="tab-pane fade">
            <h2>Gerenciamento de Serviços</h2>
            <form method="post" class="mb-4">
                <div class="form-group">
                    <label for="nome">Adicionar Novo Serviço:</label>
                    <input type="text" class="form-control" name="nome" required>
                    <input type="hidden" name="action" value="addServico">
                    <input type="hidden" name="active_tab" value="servicos">
                </div>
                <br>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Adicionar</button>
            </form>
            <?php
            $servicos = getServicosList($conn);
            ?>
            <h2>Serviços Existentes</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Serviço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($servico = $servicos->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $servico['nome'] . "</td>";
                        echo "<td>
                                <form method='post' style='display:inline-block' class='delete-form'>
                                    <input type='hidden' name='active_tab' value='servicos'>
                                    <input type='hidden' name='id' value='" . $servico['id'] . "'>
                                    <input type='hidden' name='action' value='deleteServico'>
                                    <button type='button' class='btn btn-danger delete-btn'><i class='fas fa-trash-alt'></i> Excluir</button>
                                </form>
                                <button type='button' class='btn btn-primary edit-servico-btn' data-id='".$servico['id']."' data-nome='".$servico['nome']."'><i class='fas fa-edit'></i> Editar</button>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <h2>Adicionar Serviço Atribuído</h2>
            <form method="post" class="mb-4">
                <div class="form-group">
                    <label for="atendente_id">Atendente:</label>
                    <select class="form-control" name="atendente_id" required>
                        <?php
                        $atendentes = getAtendentes($conn);
                        while ($atendente = $atendentes->fetch_assoc()) {
                            echo "<option value='{$atendente['id']}'>{$atendente['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="servico_id">Serviço:</label>
                    <select class="form-control" name="servico_id" required>
                        <?php
                        $servicos = getServicosList($conn); 
                        while ($servico = $servicos->fetch_assoc()) {
                            echo "<option value='{$servico['id']}'>{$servico['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <input type="hidden" name="action" value="addAtendenteServico">
                <input type="hidden" name="active_tab" value="servicos">
                <br>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Adicionar</button>
            </form>
            <h2>Serviços Atribuídos</h2>
            <?php
            $atendenteServicos = getAtendenteServicos($conn);
            ?>
                        <table class="table">
                <thead>
                    <tr>
                        <th>Atendente</th>
                        <th>Serviço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($atendenteServico = $atendenteServicos->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $atendenteServico['atendente'] . "</td>";
                        echo "<td>" . $atendenteServico['servico'] . "</td>";
                        echo "<td>
                                <form method='post' style='display:inline-block' class='delete-form'>
                                    <input type='hidden' name='active_tab' value='servicos'>
                                    <input type='hidden' name='id' value='" . $atendenteServico['id'] . "'>
                                    <input type='hidden' name='action' value='deleteAtendenteServico'>
                                    <button type='button' class='btn btn-danger delete-btn'><i class='fas fa-trash-alt'></i> Excluir</button>
                                </form>
                                <button type='button' class='btn btn-primary edit-serv-atendente-btn' data-id='".$atendenteServico['id']."' data-atendente='".$atendenteServico['atendente']."' data-servico='".$atendenteServico['servico']."'><i class='fas fa-edit'></i> Editar</button>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div id="horarios" class="tab-pane fade">
            <h2>Gerenciamento de Horários</h2>
            <form method="post" class="mb-4">
                <div class="form-group">
                    <label for="horario">Adicionar Novo Horário:</label>
                    <input type="time" class="form-control" name="horario" required>
                    <input type="hidden" name="action" value="addHorario">
                    <input type="hidden" name="active_tab" value="horarios">
                </div>
                <br>        
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Adicionar</button>
            </form>
            <?php
            $horarios = getHorariosList($conn);
            ?>
            <h2>Horários Existentes</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Horário</th>
                        <th>Disponibilidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($horario = $horarios->fetch_assoc()) {
                        $disponibilidade = $horario['disponivel'] ? 'Disponível' : 'Indisponível';
                        $disponibilidadeBtn = $horario['disponivel'] ? 'Indisponibilizar' : 'Disponibilizar';
                        echo "<tr>";
                        echo "<td>" . $horario['horario'] . "</td>";
                        echo "<td>" . $disponibilidade . "</td>";
                        echo "<td>
                                <form method='post' style='display:inline-block' class='delete-form'>
                                    <input type='hidden' name='id' value='" . $horario['id'] . "'>
                                    <input type='hidden' name='action' value='deleteHorario'>
                                    <input type='hidden' name='active_tab' value='horarios'>
                                    <button type='button' class='btn btn-danger delete-btn'><i class='fas fa-trash-alt'></i> Excluir</button>
                                </form>
                                <button type='button' class='btn btn-primary edit-horario-btn' data-id='".$horario['id']."' data-horario='".$horario['horario']."'><i class='fas fa-edit'></i> Editar</button>
                                <form method='post' style='display:inline-block' class='disponibilidade-form'>
                                    <input type='hidden' name='id' value='" . $horario['id'] . "'>
                                    <input type='hidden' name='action' value='setDisponivel'>
                                    <input type='hidden' name='disponivel' value='".($horario['disponivel'] ? 0 : 1)."'>
                                    <input type='hidden' name='active_tab' value='horarios'>
                                    <button type='button' class='btn btn-secondary disponibilidade-btn'>$disponibilidadeBtn</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div id="atendentes" class="tab-pane fade">
            <h2>Gerenciamento de Atendentes</h2>
            <form method="post" class="mb-4">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" class="form-control" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="cargo">Cargo:</label>
                    <input type="text" class="form-control" name="cargo" required>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" name="disponivel" id="disponivel">
                    <label class="form-check-label" for="disponivel">Disponível</label>
                </div>
                <input type="hidden" name="action" value="addAtendente">
                <input type="hidden" name="active_tab" value="atendentes">
                <br>        
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Adicionar</button>
            </form>
            <?php
            $atendentes = getAtendentes($conn);
            ?>
            <h2>Atendentes Existentes</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cargo</th>
                        <th>Disponibilidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($atendente = $atendentes->fetch_assoc()) {
                        $disponibilidade = $atendente['disponivel'] ? 'Disponível' : 'Indisponível';
                        $disponibilidadeBtn = $atendente['disponivel'] ? 'Indisponibilizar' : 'Disponibilizar';
                        echo "<tr>";
                        echo "<td>" . $atendente['nome'] . "</td>";
                        echo "<td>" . $atendente['cargo'] . "</td>";
                        echo "<td>" . $disponibilidade . "</td>";
                        echo "<td>
                                <form method='post' style='display:inline-block' class='delete-form'>
                                    <input type='hidden' name='id' value='" . $atendente['id'] . "'>
                                    <input type='hidden' name='action' value='deleteAtendente'>
                                    <input type='hidden' name='active_tab' value='atendentes'>
                                    <button type='button' class='btn btn-danger delete-btn'><i class='fas fa-trash-alt'></i> Excluir</button>
                                </form>
                                <button type='button' class='btn btn-primary edit-atendente-btn' data-id='".$atendente['id']."' data-nome='".$atendente['nome']."' data-cargo='".$atendente['cargo']."' data-disponivel='".$atendente['disponivel']."'><i class='fas fa-edit'></i> Editar</button>
                                <form method='post' style='display:inline-block' class='disponibilidade-form'>
                                    <input type='hidden' name='id' value='" . $atendente['id'] . "'>
                                    <input type='hidden' name='action' value='setAtendenteDisponibilidade'>
                                    <input type='hidden' name='disponivel' value='".($atendente['disponivel'] ? 0 : 1)."'>
                                    <input type='hidden' name='active_tab' value='atendentes'>
                                    <button type='button' class='btn btn-secondary disponibilidade-btn'>$disponibilidadeBtn</button>
                                </form>    
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div id="usuarios" class="tab-pane fade">
            <h2>Gerenciamento de Usuários</h2>
            <form method="post" class="mb-4">
                <div class="form-group">
                    <label for="username">Usuário:</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <input type="hidden" name="action" value="addUser">
                <input type="hidden" name="active_tab" value="usuarios">
                <br>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Adicionar</button>
            </form>
            <?php
            $users = getUsers($conn);
            ?>
            <h2>Usuários Existentes</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($user = $users->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $user['username'] . "</td>";
                        echo "<td>
                                <form method='post' style='display:inline-block' class='delete-form'>
                                    <input type='hidden' name='id' value='" . $user['id'] . "'>
                                    <input type='hidden' name='action' value='deleteUser'>
                                    <input type='hidden' name='active_tab' value='usuarios'>
                                    <button type='button' class='btn btn-danger delete-btn'><i class='fas fa-trash-alt'></i> Excluir</button>
                                </form>
                                <button type='button' class='btn btn-primary edit-user-btn' data-id='".$user['id']."' data-username='".$user['username']."'><i class='fas fa-edit'></i> Editar</button>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div id="customizacao" class="tab-pane fade">
            <h2>Customização</h2>
            <form method="post" class="mb-4">
                <div class="form-group">
                    <label for="logo_url">URL da Logo:</label>
                    <input type="text" class="form-control" name="logo_url" value="<?php echo isset($customizacao['logo_url']) ? $customizacao['logo_url'] : 'https://i.postimg.cc/sg2BX9dg/logo.png'; ?>" required>
                </div>
                <div class="form-group">
                    <label for="navbar_color">Cor da Barra de Navegação:</label>
                    <input type="color" class="form-control" name="navbar_color" value="<?php echo isset($customizacao['navbar_color']) ? $customizacao['navbar_color'] : '#007BFF'; ?>" required>
                </div>
                <div class="form-group">
                    <label for="primary_color">Cor Primária:</label>
                    <input type="color" class="form-control" name="primary_color" value="<?php echo isset($customizacao['primary_color']) ? $customizacao['primary_color'] : '#0042DA'; ?>" required>
                </div>
                <div class="form-group">
                    <label for="footer_text">Texto do Rodapé:</label>
                    <input type="text" class="form-control" name="footer_text" value="<?php echo isset($customizacao['footer_text']) ? $customizacao['footer_text'] : 'Copyright © 2024 Pack Typebot - Feito com ❤️'; ?>" required>
                </div>
                <input type="hidden" name="action" value="customizacao">
                <input type="hidden" name="active_tab" value="customizacao">
                <br>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Customização</button>
            </form>
        </div>
</div>
    </div>
</div>
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Agendamento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="action" value="editAgendamento">
                    <input type="hidden" name="id" id="edit-id">
                    <input type="hidden" name="active_tab" value="agendamentos">
                    <div class="form-group">
                        <label for="edit-nome">Nome:</label>
                        <input type="text" class="form-control" id="edit-nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-telefone">Telefone:</label>
                        <input type="text" class="form-control" id="edit-telefone" name="telefone" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-data">Data:</label>
                        <input type="date" class="form-control" id="edit-data" name="data" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-horario">Horário:</label>
                        <input type="time" class="form-control" id="edit-horario" name="horario" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-servico">Tipo de Serviço:</label>
                        <select class="form-control" id="edit-servico" name="servico" required>
                            <?php
                            $servicos = getServicos($conn);
                            while ($servico = $servicos->fetch_assoc()) {
                                echo "<option value='{$servico['nome']}'>{$servico['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-atendente">Atendente:</label>
                        <select class="form-control" id="edit-atendente" name="atendente" required>
                            <?php
                            $atendentes = getAtendentes($conn);
                            while ($atendente = $atendentes->fetch_assoc()) {
                                echo "<option value='{$atendente['nome']}'>{$atendente['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editServicoModal" tabindex="-1" aria-labelledby="editServicoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editServicoLabel">Editar Serviço</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editServicoForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="action" value="updateServico">
                    <input type="hidden" name="id" id="edit-servico-id">
                    <input type="hidden" name="active_tab" value="servicos">
                    <div class="form-group">
                        <label for="edit-servico-name">Nome do Serviço:</label>
                        <input type="text" class="form-control" id="edit-servico-name" name="nome" required>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Editar Serviço Atribuído -->
<div class="modal fade" id="editAtendenteServicoModal" tabindex="-1" aria-labelledby="editAtendenteServicoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAtendenteServicoLabel">Editar Serviço Atribuído</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editAtendenteServicoForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="action" value="updateAtendenteServico">
                    <input type="hidden" name="id" id="edit-atendente-servico-id">
                    <input type="hidden" name="active_tab" value="servicos">
                    <div class="form-group">
                        <label for="edit-atendente-serv-id">Atendente:</label>
                        <select class="form-control" id="edit-atendente-serv-id" name="atendente_id" required>
                            <?php
                            $atendentes = getAtendentes($conn);
                            while ($atendente = $atendentes->fetch_assoc()) {
                                echo "<option value='{$atendente['id']}'>{$atendente['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-servico-serv-id">Serviço:</label>
                        <select class="form-control" id="edit-servico-serv-id" name="servico_id" required>
                            <?php
                            $servicos = getServicosList($conn); // Re-fetching the services for dropdown
                            while ($servico = $servicos->fetch_assoc()) {
                                echo "<option value='{$servico['id']}'>{$servico['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editHorarioModal" tabindex="-1" aria-labelledby="editHorarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editHorarioLabel">Editar Horário</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editHorarioForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="action" value="updateHorario">
                    <input type="hidden" name="id" id="edit-horario-id">
                    <input type="hidden" name="active_tab" value="horarios">
                    <div class="form-group">
                        <label for="edit-horario-time">Horário:</label>
                        <input type="time" class="form-control" id="edit-horario-time" name="horario" required>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Editar Atendente -->
<div class="modal fade" id="editAtendenteModal" tabindex="-1" aria-labelledby="editAtendenteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAtendenteLabel">Editar Atendente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editAtendenteForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="action" value="updateAtendente">
                    <input type="hidden" name="id" id="edit-atendente-id">
                    <input type="hidden" name="active_tab" value="atendentes">
                    <div class="form-group">
                        <label for="edit-atendente-nome">Nome do Atendente:</label>
                        <input type="text" class="form-control" id="edit-atendente-nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-atendente-cargo">Cargo:</label>
                        <input type="text" class="form-control" id="edit-atendente-cargo" name="cargo" required>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="edit-atendente-disponivel" name="disponivel">
                        <label class="form-check-label" for="edit-atendente-disponivel">Disponível</label>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserLabel">Editar Usuário</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="action" value="updateUser">
                    <input type="hidden" name="id" id="edit-user-id">
                    <input type="hidden" name="active_tab" value="usuarios">
                    <div class="form-group">
                        <label for="edit-username">Usuário:</label>
                        <input type="text" class="form-control" id="edit-username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-password">Senha:</label>
                        <input type="password" class="form-control" id="edit-password" name="password" required>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (isset($_SESSION['msg'])): ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['msg_type']; ?>',
                title: '<?php echo $_SESSION['msg_type'] == "success" ? "Sucesso" : "Erro"; ?>',
                text: '<?php echo $_SESSION['msg']; ?>'
            });
            <?php unset($_SESSION['msg']); ?>
            <?php unset($_SESSION['msg_type']); ?>
        <?php endif; ?>
        const activeTab = '<?php echo isset($_SESSION["active_tab"]) ? $_SESSION["active_tab"] : "agendamentos"; unset($_SESSION["active_tab"]); ?>';
        document.getElementById(activeTab + '-tab').classList.add('active');
        document.getElementById(activeTab).classList.add('show', 'active');
        const deleteButtons = document.querySelectorAll('.delete-btn, .delete-evolutionapi-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('form');
                Swal.fire({
                    title: 'Tem certeza?',
                    text: "Você não poderá reverter isso!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim, excluir!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
        const disponibilidadeButtons = document.querySelectorAll('.disponibilidade-btn');
        disponibilidadeButtons.forEach(button => {
            button.addEventListener('click', function () {
                this.closest('form.disponibilidade-form').submit();
            });
        });
        const editAgendamentoButtons = document.querySelectorAll('.edit-btn');
        editAgendamentoButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const nome = this.getAttribute('data-nome');
                const telefone = this.getAttribute('data-telefone');
                const data = this.getAttribute('data-data');
                const horario = this.getAttribute('data-horario');
                const servico = this.getAttribute('data-servico');
                const atendente = this.getAttribute('data-atendente');
                document.getElementById('edit-id').value = id;
                document.getElementById('edit-nome').value = nome;
                document.getElementById('edit-telefone').value = telefone;
                document.getElementById('edit-data').value = data;
                document.getElementById('edit-horario').value = horario;
                document.getElementById('edit-servico').value = servico;
                document.getElementById('edit-atendente').value = atendente;
                $('#editModal').modal('show');
            });
        });
        const editServicoButtons = document.querySelectorAll('.edit-servico-btn');
        editServicoButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const nome = this.getAttribute('data-nome');
                document.getElementById('edit-servico-id').value = id;
                document.getElementById('edit-servico-name').value = nome;
                $('#editServicoModal').modal('show');
            });
        });
const editServAtendenteButtons = document.querySelectorAll('.edit-serv-atendente-btn');
editServAtendenteButtons.forEach(button => {
    button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const atendente = this.getAttribute('data-atendente');
        const servico = this.getAttribute('data-servico');
        // Passar valores para o modal de edição do serviço atribuído
        document.getElementById('edit-atendente-servico-id').value = id;
        document.getElementById('edit-atendente-serv-id').value = atendente;
        document.getElementById('edit-servico-serv-id').value = servico;
        // Mostrar modal
        $('#editAtendenteServicoModal').modal('show');
    });
});
        const editHorarioButtons = document.querySelectorAll('.edit-horario-btn');
        editHorarioButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const horario = this.getAttribute('data-horario');
                document.getElementById('edit-horario-id').value = id;
                document.getElementById('edit-horario-time').value = horario;
                $('#editHorarioModal').modal('show');
            });
        });
const editAtendenteButtons = document.querySelectorAll('.edit-atendente-btn');
editAtendenteButtons.forEach(button => {
    button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const nome = this.getAttribute('data-nome');
        const cargo = this.getAttribute('data-cargo');
        const disponivel = this.getAttribute('data-disponivel') == '1';
        // Passar valores para o modal de edição do atendente
        document.getElementById('edit-atendente-id').value = id;
        document.getElementById('edit-atendente-nome').value = nome;
        document.getElementById('edit-atendente-cargo').value = cargo;
        document.getElementById('edit-atendente-disponivel').checked = disponivel;
        // Mostrar modal
        $('#editAtendenteModal').modal('show');
    });
});
        const editUserButtons = document.querySelectorAll('.edit-user-btn');
        editUserButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const username = this.getAttribute('data-username');
                document.getElementById('edit-user-id').value = id;
                document.getElementById('edit-username').value = username;
                $('#editUserModal').modal('show');
            });
        });
        const copyButtons = document.querySelectorAll('.copy-btn');
        copyButtons.forEach(button => {
            button.addEventListener('click', function () {
                const url = this.getAttribute('data-url');
                const tempInput = document.createElement('input');
                document.body.appendChild(tempInput);
                tempInput.value = url;
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                Swal.fire({
                    icon: 'success',
                    title: 'Copiado!',
                    text: 'URL copiada para a área de transferência.'
                });
            });
        });
    });
</script>
<footer>
    <div class="footer">
        <p><?php echo isset($customizacao['footer_text']) ? $customizacao['footer_text'] : 'Copyright © 2024 Pack Typebot - Feito com ❤️'; ?></p>
    </div>
</footer>
</body>
</html>