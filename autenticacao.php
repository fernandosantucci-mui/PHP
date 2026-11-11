<?php
session_start();
include_once "conexao.php";

// Verifica se a ligação falhou logo no início
if (!$conn) {
    die("Erro de conexão no login: " . mysqli_connect_error());
}

// Verifica se os campos foram enviados
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';

// Limpa caracteres especiais para evitar problemas na query (SQL Injection)
$email = mysqli_real_escape_string($conn, $email);
$senha = mysqli_real_escape_string($conn, $senha);

// Buscando os dados com os nomes das colunas exatamente como criados na tabela
$sql = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    
    // CORREÇÃO: Chaves em minúsculo, combinando com o banco de dados
    $_SESSION['logado'] = true; 
    $_SESSION['email'] = $row['email']; 
    $_SESSION['usuario'] = $row['nome'];
    $_SESSION['dsEmail'] = $row['email'];
    $_SESSION['senha'] = $row['senha'];
    
    // Redireciona para o menu principal
    header('Location: menu.php');
    exit;
} else {
    // Alerta em caso de erro
    echo "<script>
        alert('Login incorreto! O sistema procurou por:\\nE-mail: " . $email . "\\nSenha: " . $senha . "'); 
        window.location.href='index_2.html';
    </script>";
    exit;
}
?>