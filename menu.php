<?php
// Força a exibição de erros (caso o 500 persista, o erro real aparecerá na tela)
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Proteção da página
if (!isset($_SESSION['logado'])) { 
    header('Location: index_2.html'); 
    exit; 
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Menu Principal</title>
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
            min-height: 100vh;
        }

        .sidebar {
            background-color: #1e1e1e;
            width: 280px;
            min-height: 100vh;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.5);
            border-right: 3px solid #d4af37;
            z-index: 10;
        }

        h2 {
            color: #d4af37; 
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: center;
            border-bottom: 1px solid #333333; 
            padding-bottom: 20px;
            font-size: 1.1rem;
        }

        .welcome-box {
            background-color: #252525;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #d4af37;
            text-align: center;
            margin-bottom: 30px;
            font-size: 0.95rem;
            color: #ccc;
        }

        .welcome-box strong {
            color: #ffffff;
            letter-spacing: 0.5px;
            display: block;
            margin-top: 5px;
            word-break: break-all;
        }

        .menu-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 15px;
            flex: 1; 
        }

        .menu-list a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background-color: #2a2a2a; 
            border: 1px solid #444444;
            border-left: 4px solid #b30000; 
            color: #d4af37; 
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .menu-list a:hover {
            background-color: #b30000; 
            border-color: #b30000;
            color: #ffffff; 
            transform: translateX(8px); 
            box-shadow: 0 8px 20px rgba(230, 0, 0, 0.3);
        }

        .acoes {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #333333;
        }

        .btn-outline-red {
            display: block;
            text-align: center;
            padding: 12px 20px;
            color: #ff4d4d;
            background-color: transparent;
            border: 2px solid #b30000;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-outline-red:hover {
            background-color: #b30000;
            color: #ffffff;
            box-shadow: 0 6px 15px rgba(230, 0, 0, 0.4);
            transform: translateY(-2px);
        }

        .main-content {
            flex: 1;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #121212;
        }

        .content-panel {
            background-color: #1e1e1e;
            width: 100%;
            max-width: 900px;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 15px rgba(212, 175, 55, 0.05);
            text-align: center;
        }

        .content-panel h3 {
            color: #d4af37;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .content-panel p {
            color: #888;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                min-height: auto;
                border-right: none;
                border-bottom: 3px solid #d4af37;
            }
            .main-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h2>Painel - Obras</h2>
        
        <div class="welcome-box">
            Bem-vindo(a),<br> 
            <strong><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Usuário'; ?></strong>
        </div>
        
        <ul class="menu-list">
            <li><a href="obras.php">Gestão de Obras</a></li>
            <li><a href="engenheiros.php">Engenheiros</a></li>
            <li><a href="orcamentos.php">Orçamentos</a></li>
            <li><a href="fases.php">Manutenção de Fases</a></li>
            <li><a href="graficos.php">Gráficos / Dashboard</a></li>
        </ul>

        <div class="acoes">
            <a href="logout.php" class="btn-outline-red">Sair do Sistema</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="content-panel">
            <h3>Visão Geral</h3>
            <p>Selecione uma opção no menu lateral para gerenciar o sistema.</p>
        </div>
    </main>
</body>
</html>