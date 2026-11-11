<?php
session_start();
if (!isset($_SESSION['logado'])) { header('Location: index_2.html'); exit; }
include_once "conexao.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
// É recomendável forçar o ID a ser um número inteiro por segurança
$idEngenheiro = isset($_POST['idEngenheiro']) ? intval($_POST['idEngenheiro']) : (isset($_GET['id']) ? intval($_GET['id']) : null);

// Escapar dados para evitar erro com aspas e SQL Injection
$dsNome = mysqli_real_escape_string($conn, $_POST['dsNome'] ?? '');
$nrCREA = mysqli_real_escape_string($conn, $_POST['nrCREA'] ?? '');
$dtAdmissao = mysqli_real_escape_string($conn, $_POST['dtAdmissao'] ?? '');

if ($acao == 'incluir') {
    // Como idEngenheiro é AUTO_INCREMENT no banco, não passamos ele no INSERT.
    // As colunas corretas do banco são: nome, crea e data_admissao
    $sql = "INSERT INTO engenheiros (nome, crea, data_admissao) VALUES ('$dsNome', '$nrCREA', '$dtAdmissao')";
    
    if (!mysqli_query($conn, $sql)) {
        die("Erro ao salvar na base de dados: " . mysqli_error($conn));
    }
} 
elseif ($acao == 'alterar' && $idEngenheiro) {
    // Atualizado para usar os nomes corretos das colunas no banco de dados
    $sql = "UPDATE engenheiros SET nome = '$dsNome', crea = '$nrCREA', data_admissao = '$dtAdmissao' WHERE idEngenheiro = $idEngenheiro";
    
    if (!mysqli_query($conn, $sql)) {
        die("Erro ao alterar na base de dados: " . mysqli_error($conn));
    }
} 
elseif ($acao == 'excluir' && $idEngenheiro) {
    $sql = "DELETE FROM engenheiros WHERE idEngenheiro = $idEngenheiro";
    
    if (!mysqli_query($conn, $sql)) {
        die("Erro ao excluir na base de dados: " . mysqli_error($conn));
    }
}

header('Location: engenheiros.php');
exit;
?>