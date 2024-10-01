<?php
function getConnection()
{
    $host = "localhost";
    $user = "seu_username";
    $password = "seu_password";
    $dbname = "seu_banco_de_dados";

    // Cria a conexão
    $conn = mysqli_connect($host, $user, $password, $dbname);
    mysqli_set_charset($conn, "utf8");

    // Verifica se a conexão foi criada com sucesso
    if (!$conn) {
        die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
    }

    return $conn;
}
?>