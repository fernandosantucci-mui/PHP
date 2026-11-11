<?php
session_start();
if (!isset($_SESSION['logado'])) { header('Location: index_2.html'); exit; }
include_once "conexao.php";

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

if ($acao === 'incluir') {
    $dsFase = mysqli_real_escape_string($conn, $_POST['dsFase'] ?? '');
    $sql = "INSERT INTO fases (dsFase) VALUES ('$dsFase')";
    if (!mysqli_query($conn, $sql)) die("Erro: " . mysqli_error($conn));
} 
elseif ($acao === 'alterar') {
    $idFase = (int)($_POST['idFase'] ?? 0);
    $dsFase = mysqli_real_escape_string($conn, $_POST['dsFase'] ?? '');
    $sql = "UPDATE fases SET dsFase = '$dsFase' WHERE idFase = $idFase";
    if (!mysqli_query($conn, $sql)) die("Erro: " . mysqli_error($conn));
} 
elseif ($acao === 'excluir') {
    $idFase = (int)($_GET['id'] ?? 0);
    $sql = "DELETE FROM fases WHERE idFase = $idFase";
    if (!mysqli_query($conn, $sql)) die("Erro: " . mysqli_error($conn));
}

header('Location: fases.php');
exit;
?>