<?php
session_start();
if (!isset($_SESSION['logado'])) { header('Location: index_2.html'); exit; }
include_once "conexao.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$idOrcamento = (int)($_POST['idOrcamento'] ?? $_GET['id'] ?? 0);

// CORREÇÃO: Captura do responsável
$responsavel = mysqli_real_escape_string($conn, $_POST['responsavel'] ?? '');
$status = $_POST['status'] ?? 'pendente';

if ($status === 'pendente' || empty($_POST['dtAprovacao'])) {
    $dtAprovacao = "NULL"; 
} else {
    $dtAprovacao = "'" . mysqli_real_escape_string($conn, $_POST['dtAprovacao']) . "'";
}

if ($acao == 'incluir') {
    // CORREÇÃO: Removido SELECT MAX(id), o auto_increment faz isto sozinho. Inserindo nas colunas corretas.
    $sql = "INSERT INTO orcamentos (data_aprovacao, responsavel_elaboracao) VALUES ($dtAprovacao, '$responsavel')";
    if (!mysqli_query($conn, $sql)) {
        die("Erro ao salvar no banco de dados: " . mysqli_error($conn));
    }
} 
elseif ($acao == 'alterar' && $idOrcamento) {
    // CORREÇÃO: Colunas corretas
    $sql = "UPDATE orcamentos SET data_aprovacao = $dtAprovacao, responsavel_elaboracao = '$responsavel' WHERE numero_orcamento = $idOrcamento";
    if (!mysqli_query($conn, $sql)) {
        die("Erro ao alterar no banco de dados: " . mysqli_error($conn));
    }
} 
elseif ($acao == 'excluir' && $idOrcamento) {
    // CORREÇÃO: Coluna correta
    $sql = "DELETE FROM orcamentos WHERE numero_orcamento = $idOrcamento";
    if (!mysqli_query($conn, $sql)) {
        die("Erro ao excluir no banco de dados: " . mysqli_error($conn));
    }
}

header('Location: orcamentos.php');
exit;
?>