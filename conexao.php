<?php
// Impede que o driver do MySQLi lance exceções fatais que causam o Erro 500
mysqli_report(MYSQLI_REPORT_OFF);

$localhost = "localhost"; // O MySQL é local para o servidor onde o PHP executa
$user = "root";
$password = "12345";      // Se o root não tiver senha no servidor, deixe ""
$banco = "Fernando_ifti3";

$conn = @mysqli_connect($localhost, $user, $password, $banco);

if (!$conn) {
    die("Erro ao ligar ao banco de dados: " . mysqli_connect_error());
}
?>