<?php
session_start();
if (!isset($_SESSION['logado'])) { header('Location: index_2.html'); exit; }
include_once "conexao.php";

$idOrcamento = $_GET['id'] ?? null;
$responsavel = '';
$dtAprovacao = '';
$status = 'pendente';

if ($idOrcamento) {
    // CORREÇÃO: Nomes corretos das colunas (numero_orcamento, responsavel_elaboracao, data_aprovacao)
    $sql = "SELECT responsavel_elaboracao, data_aprovacao FROM orcamentos WHERE numero_orcamento = $idOrcamento";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        die("Erro na base de dados: " . mysqli_error($conn));
    }

    if ($row = mysqli_fetch_assoc($result)) {
        $responsavel = $row['responsavel_elaboracao'] ?? '';
        $dtAprovacao = $row['data_aprovacao'] ?? '';
        
        if (!empty($dtAprovacao) && $dtAprovacao != '0000-00-00') {
            $status = 'aprovado';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title><?= $idOrcamento ? 'Editar' : 'Novo' ?> Orçamento</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #121212; color: #f1f1f1; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { background-color: #1e1e1e; width: 100%; max-width: 500px; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 15px rgba(212, 175, 55, 0.05); border-top: 5px solid #d4af37; }
        h2 { color: #d4af37; margin-bottom: 30px; text-transform: uppercase; letter-spacing: 2px; text-align: center; border-bottom: 1px solid #333333; padding-bottom: 20px; }
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; color: #cccccc; font-weight: bold; letter-spacing: 0.5px; }
        input[type="text"], input[type="date"], select { width: 100%; padding: 12px 15px; background-color: #252525; border: 1px solid #444444; border-radius: 6px; color: #ffffff; font-size: 1rem; transition: all 0.3s ease; }
        input[type="text"]:focus, input[type="date"]:focus, select:focus { outline: none; border-color: #d4af37; box-shadow: 0 0 8px rgba(212, 175, 55, 0.3); background-color: #2a2a2a; }
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); }
        .acoes { display: flex; justify-content: space-between; align-items: center; margin-top: 35px; }
        .btn { display: inline-block; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; cursor: pointer; text-align: center; transition: all 0.3s ease; font-size: 1rem; }
        .btn:hover { transform: translateY(-2px); }
        .btn-gray { background-color: transparent; color: #a0a0a0; border: 1px solid #555555; }
        .btn-gray:hover { background-color: #333333; color: #ffffff; border-color: #ffffff; }
        .btn-red { background-color: #b30000; color: #ffffff; border: none; }
        .btn-red:hover { background-color: #e60000; box-shadow: 0 4px 12px rgba(230, 0, 0, 0.4); }
    </style>
</head>
<body>
    <div class="container">
        <h2><?= $idOrcamento ? 'Editar' : 'Novo' ?> Orçamento</h2>
        
        <form method="post" action="orcamento_acao.php">
            <input type="hidden" name="acao" value="<?= $idOrcamento ? 'alterar' : 'incluir' ?>">
            <?php if ($idOrcamento): ?>
                <input type="hidden" name="idOrcamento" value="<?= $idOrcamento ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Responsável pela Elaboração</label>
                <!-- CORREÇÃO: Alterado de Data do Orçamento para Responsável -->
                <input type="text" name="responsavel" value="<?= htmlspecialchars($responsavel) ?>" required autofocus>
            </div>

            <div class="form-group">
                <label>Estado do Orçamento</label>
                <select name="status" id="statusSelect" onchange="toggleDataAprovacao()">
                    <option value="pendente" <?= $status == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                    <option value="aprovado" <?= $status == 'aprovado' ? 'selected' : '' ?>>Aprovado</option>
                </select>
            </div>
            
            <div class="form-group" id="grupoAprovacao" style="<?= $status == 'pendente' ? 'display: none;' : '' ?>">
                <label>Data de Aprovação</label>
                <input type="date" name="dtAprovacao" id="dtAprovacaoInput" value="<?= htmlspecialchars($dtAprovacao) ?>">
            </div>

            <div class="acoes">
                <a href="orcamentos.php" class="btn btn-gray">Cancelar</a>
                <button type="submit" class="btn btn-red">Salvar Dados</button>
            </div>
        </form>
    </div>

    <script>
        function toggleDataAprovacao() {
            const status = document.getElementById('statusSelect').value;
            const grupoAprovacao = document.getElementById('grupoAprovacao');
            const inputAprovacao = document.getElementById('dtAprovacaoInput');

            if (status === 'pendente') {
                grupoAprovacao.style.display = 'none';
                inputAprovacao.value = '';
                inputAprovacao.removeAttribute('required');
            } else {
                grupoAprovacao.style.display = 'block';
                inputAprovacao.setAttribute('required', 'required');
            }
        }
    </script>
</body>
</html>