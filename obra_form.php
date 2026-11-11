<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['logado'])) { header('Location: index_2.html'); exit; }
include_once "conexao.php";

$idObra = $_GET['id'] ?? null;
$dsObra = '';
$dtInicio = ''; 
$dtFim = ''; 
$idEngenheiroSelecionado = '';

if ($idObra) {
    // CORREÇÃO: Nomes de colunas ajustados para bater com a tabela obras
    $sql = "SELECT descricao, id_engenheiro_responsavel, data_inicio, data_final FROM obras WHERE numero_obra = $idObra";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        die("Erro na base de dados (Obras): " . mysqli_error($conn));
    }

    if ($row = mysqli_fetch_assoc($result)) {
        $dsObra = $row['descricao'];
        $idEngenheiroSelecionado = $row['id_engenheiro_responsavel'];
        $dtInicio = $row['data_inicio'] ?? ''; 
        $dtFim = $row['data_final'] ?? ''; 
    }
}

// CORREÇÃO AQUI: 'nome' em vez de 'dsNome'
$sqlEng = "SELECT idEngenheiro, nome FROM engenheiros ORDER BY nome ASC";
$resultEng = mysqli_query($conn, $sqlEng);
if (!$resultEng) {
    die("Erro ao carregar engenheiros: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title><?= $idObra ? 'Editar' : 'Nova' ?> Obra</title>
    <!-- Mantenha as suas tags de <style> aqui -->
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #121212; color: #f1f1f1; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { background-color: #1e1e1e; width: 100%; max-width: 500px; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); border-top: 5px solid #d4af37; }
        h2 { color: #d4af37; margin-bottom: 30px; text-transform: uppercase; text-align: center; border-bottom: 1px solid #333333; padding-bottom: 20px; }
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; color: #cccccc; font-weight: bold; }
        input[type="text"], input[type="date"], select { width: 100%; padding: 12px; background-color: #252525; border: 1px solid #444444; border-radius: 6px; color: #ffffff; font-size: 1rem; }
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); }
        .acoes { display: flex; justify-content: space-between; margin-top: 35px; }
        .btn { padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; cursor: pointer; text-align: center; }
        .btn-gray { background-color: transparent; color: #a0a0a0; border: 1px solid #555555; }
        .btn-red { background-color: #b30000; color: #ffffff; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2><?= $idObra ? 'Editar' : 'Nova' ?> Obra</h2>
        <form method="post" action="obra_acao.php">
            <input type="hidden" name="acao" value="<?= $idObra ? 'alterar' : 'incluir' ?>">
            <?php if ($idObra): ?>
                <input type="hidden" name="idObra" value="<?= $idObra ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Nome da Obra</label>
                <input type="text" name="nomeObra" value="<?= htmlspecialchars($dsObra) ?>" required autofocus>
            </div>
            
            <div class="form-group">
                <label>Data de Início</label>
                <input type="date" name="dtInicio" value="<?= htmlspecialchars($dtInicio) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Data de Fim</label>
                <!-- Removido 'required' porque a data_final no banco permite nulo -->
                <input type="date" name="dtFim" value="<?= htmlspecialchars($dtFim) ?>">
            </div>

            <div class="form-group">
                <label>Engenheiro Responsável</label>
                <select name="idEngenheiro" required>
                    <option value="">Selecione um Engenheiro</option>
                    <?php while ($eng = mysqli_fetch_assoc($resultEng)): ?>
                        <!-- CORREÇÃO AQUI: 'nome' em vez de 'dsNome' -->
                        <option value="<?= $eng['idEngenheiro'] ?>" <?= ($idEngenheiroSelecionado == $eng['idEngenheiro']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($eng['nome']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="acoes">
                <a href="obras.php" class="btn btn-gray">Cancelar</a>
                <button type="submit" class="btn btn-red">Salvar Dados</button>
            </div>
        </form>
    </div>
</body>
</html>