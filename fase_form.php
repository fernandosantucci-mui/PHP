<?php
session_start();
// Corrigido para index_2.html para evitar erros de página não encontrada
if (!isset($_SESSION['logado'])) { header('Location: index_2.html'); exit; }
include_once "conexao.php";

$idFase = $_GET['id'] ?? null;
$dsFase = '';

// Se tiver ID, busca os dados para Alterar[cite: 35]
if ($idFase) {
    $sql = "SELECT dsFase FROM fases WHERE idFase = $idFase";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $dsFase = $row['dsFase'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $idFase ? 'Editar' : 'Nova' ?> Fase</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #121212; 
            color: #f1f1f1; 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: #1e1e1e; 
            width: 100%;
            max-width: 500px; /* Largura mais compacta definida para o formulário[cite: 35] */
            padding: 40px; 
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 15px rgba(212, 175, 55, 0.05); 
            border-top: 5px solid #d4af37; 
        }

        h2 {
            color: #d4af37; 
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: center;
            border-bottom: 1px solid #333333; 
            padding-bottom: 20px;
        }

        /* Estilos do Formulário */
        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #cccccc;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            background-color: #252525;
            border: 1px solid #444444;
            border-radius: 6px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #d4af37; /* Borda dourada ao focar no campo */
            box-shadow: 0 0 8px rgba(212, 175, 55, 0.3);
            background-color: #2a2a2a;
        }

        /* Botões */
        .acoes {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 35px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn:hover { 
            transform: translateY(-2px);
        }

        .btn-gray { 
            background-color: transparent; 
            color: #a0a0a0;
            border: 1px solid #555555;
        }

        .btn-gray:hover { 
            background-color: #333333; 
            color: #ffffff;
            border-color: #ffffff;
        }

        .btn-red { 
            background-color: #b30000; 
            color: #ffffff;
            border: none;
        }

        .btn-red:hover { 
            background-color: #e60000; 
            box-shadow: 0 4px 12px rgba(230, 0, 0, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><?= $idFase ? 'Editar' : 'Nova' ?> Fase</h2>
        
        <form method="post" action="fase_acao.php">
            <input type="hidden" name="acao" value="<?= $idFase ? 'alterar' : 'incluir' ?>">
            <?php if ($idFase): ?>
                <input type="hidden" name="idFase" value="<?= $idFase ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Descrição da Fase</label>
                <input type="text" name="dsFase" value="<?= htmlspecialchars($dsFase) ?>" required autofocus>
            </div>

            <div class="acoes">
                <a href="fases.php" class="btn btn-gray">Cancelar</a>
                <button type="submit" class="btn btn-red">Salvar Dados</button>
            </div>
        </form>
    </div>
</body>
</html>