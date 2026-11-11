<?php
session_start();
if (!isset($_SESSION['logado'])) { header('Location: index_2.html'); exit; }
include_once "conexao.php";

$sql = "SELECT * FROM fases ORDER BY idFase ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Manutenção de Fases</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #121212; color: #f1f1f1; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; padding: 40px 20px; }
        .container { background-color: #1e1e1e; width: 100%; max-width: 1000px; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 15px rgba(212, 175, 55, 0.05); border-top: 5px solid #d4af37; }
        h2 { color: #d4af37; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 2px; text-align: center; border-bottom: 1px solid #333333; padding-bottom: 20px; }
        
        /* Botões */
        .btn { display: inline-block; padding: 10px 20px; color: #d4af37; background-color: #333333; border: 1px solid #d4af37; border-radius: 6px; text-decoration: none; font-weight: bold; cursor: pointer; text-align: center; transition: all 0.3s ease; }
        .btn:hover { background-color: #444444; transform: translateY(-2px); }
        .btn-red { background-color: #b30000; color: #ffffff; border: none; }
        .btn-red:hover { background-color: #e60000; box-shadow: 0 4px 12px rgba(230, 0, 0, 0.4); }
        .btn-gray { background-color: transparent; color: #a0a0a0; border: 1px solid #555555; }
        .btn-gray:hover { background-color: #333333; color: #ffffff; border-color: #ffffff; }
        .acoes-top { display: flex; justify-content: space-between; margin-bottom: 30px; }

        /* Novo Layout em Cartões (Cards) */
        .grid-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        .card { background-color: #252525; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-top: 4px solid #b30000; display: flex; flex-direction: column; transition: transform 0.2s ease; }
        .card:hover { transform: translateY(-5px); background-color: #2a2a2a; }
        
        .card-header { padding: 16px; background-color: #1e1e1e; border-bottom: 1px solid #333333; display: flex; justify-content: space-between; align-items: center; }
        .card-header h3 { color: #f1f1f1; font-size: 16px; text-transform: uppercase; margin: 0; }
        .card-header .badge { background-color: #b30000; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        
        .card-body { padding: 16px; flex-grow: 1; }
        .card-info { margin-bottom: 12px; display: flex; flex-direction: column; }
        .card-info span.label { font-size: 12px; color: #a0a0a0; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }
        .card-info span.value { font-size: 14px; color: #e0e0e0; }
        
        .card-footer { padding: 16px; background-color: #1e1e1e; border-top: 1px solid #333333; display: flex; justify-content: space-between; }
        .card-footer .btn { padding: 8px 16px; font-size: 13px; width: 48%; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Fases da Obra</h2>
        
        <div class="acoes-top">
            <a href="menu.php" class="btn btn-gray">← Voltar</a>
            <a href="fase_form.php" class="btn btn-red">+ Nova Fase</a>
        </div>

        <div class="grid-cards">
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <div class="card">
                    <div class="card-header">
                        <h3>Fase</h3>
                        <span class="badge">#<?= htmlspecialchars($row['idFase']) ?></span>
                    </div>
                    
                    <div class="card-body">
                        <div class="card-info">
                            <span class="label">Descrição da Fase</span>
                            <span class="value"><?= htmlspecialchars($row['dsFase'] ?? '-') ?></span>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <a href="fase_form.php?id=<?= $row['idFase'] ?>" class="btn">Editar</a>
                        <a href="fase_acao.php?acao=excluir&id=<?= $row['idFase'] ?>" class="btn btn-red" onclick="return confirm('Tem a certeza que deseja excluir esta fase?');">Excluir</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>