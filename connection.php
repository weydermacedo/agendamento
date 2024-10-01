<?php

class CreateConnection // Cria uma classe para estabelecer uma conexão
{
    var $servername = "localhost";
    var $username = "seu_username"; // Especifica os detalhes do servidor para o MySQL
    var $password = "seu_password";
    var $database = "seu_banco_de_dados";

    var $myconn;

    function connectToDatabase() // Cria uma função para conectar ao banco de dados
    {
        $conn = mysqli_connect($this->servername, $this->username, $this->password, $this->database);
        mysqli_set_charset($conn, "utf8mb4");

        if (!$conn) // Testa a conexão
        {
            die("Não é possível se conectar ao banco de dados");
        } else {
            $this->myconn = $conn;
        }

        return $this->myconn;
    }

    function selectDatabase() // Seleciona o banco de dados
    {
        mysqli_select_db($this->myconn, $this->database); // Usa funções internas do PHP para selecionar o banco de dados
        
        if (mysqli_error($this->myconn)) // Se ocorrer um erro, exibe a mensagem de erro
        {
            echo "Não é possível encontrar o banco de dados " . $this->database;
        }
    }

    function closeConnection() // Fecha a conexão
    {
        mysqli_close($this->myconn);
    }
}

?>
