<?php
session_start();
if (!isset($_SESSION['logado'])) { header('Location: index_2.html'); exit; }
include_once "conexao.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$idObra = (int)($_POST['idObra'] ?? $_GET['id'] ?? 0);
$dsObra = mysqli_real_escape_string($conn, $_POST['nomeObra'] ?? ''); 
$idEngenheiro = (int)($_POST['idEngenheiro'] ?? 0);
$dtInicio = mysqli_real_escape_string($conn, $_POST['dtInicio'] ?? ''); 
$dtFim = mysqli_real_escape_string($conn, $_POST['dtFim'] ?? ''); 

// Tratamento para data final vazia (permite NULL no banco)
$dtFimSQL = !empty($dtFim) ? "'$dtFim'" : "NULL";

// CORREÇÃO: Utilizando as colunas (numero_obra, descricao, data_inicio, data_final, id_engenheiro_responsavel)
if ($acao == 'incluir') {
    $sql = "INSERT INTO obras (descricao, id_engenheiro_responsavel, data_inicio, data_final) 
            VALUES ('$dsObra', $idEngenheiro, '$dtInicio', $dtFimSQL)";
    if (!mysqli_query($conn, $sql)) {
        die("Erro ao salvar no banco de dados: " . mysqli_error($conn));
    }
} 
elseif ($acao == 'alterar' && $idObra) {
    $sql = "UPDATE obras SET 
                descricao = '$dsObra', 
                id_engenheiro_responsavel = $idEngenheiro, 
                data_inicio = '$dtInicio', 
                data_final = $dtFimSQL 
            WHERE numero_obra = $idObra";
    if (!mysqli_query($conn, $sql)) {
        die("Erro ao alterar no banco de dados: " . mysqli_error($conn));
    }
} 
elseif ($acao == 'excluir' && $idObra) {
    $sql = "DELETE FROM obras WHERE numero_obra = $idObra";
    if (!mysqli_query($conn, $sql)) {
        die("Erro ao excluir no banco de dados: " . mysqli_error($conn));
    }
}

header('Location: obras.php');
exit;
?>