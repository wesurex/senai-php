<?php
$host = "db";
$usuario = "root";
$senha = "root";
$banco = "minha_base";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
