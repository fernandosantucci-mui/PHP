<?php
// Ativação de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['logado'])) { header('Location: index_2.html'); exit; }
include_once "conexao.php";

// Força a codificação UTF-8 na conexão para evitar erros no JSON
mysqli_set_charset($conn, "utf8");

// --- DECLARAÇÃO DA FUNÇÃO DE DADOS ---
function obterDadosDashboard($conn) {
    // 1. Obras (Linha de Tempo)
    $obrasLabels = []; $obrasAnosInicio = []; $obrasAnosFim = []; $obrasDetalhes = [];
    $sqlObras = "SELECT o.idObra, o.dsObra, o.dtInicio, o.dtFim, COALESCE(e.dsNome, 'Sem Engenheiro') AS engenheiro 
                 FROM obras o LEFT JOIN engenheiros e ON o.idEngenheiro = e.idEngenheiro 
                 ORDER BY o.idObra ASC";
    $resObras = mysqli_query($conn, $sqlObras);
    if ($resObras) {
        while ($row = mysqli_fetch_assoc($resObras)) {
            $obrasLabels[] = (!empty($row['dsObra']) ? $row['dsObra'] : 'Obra') . " (#" . $row['idObra'] . ")";
            
            $dtIniRaw = $row['dtInicio'] ?? '';
            $dtFimRaw = $row['dtFim'] ?? '';

            $anoInicio = (!empty($dtIniRaw) && $dtIniRaw !== '0000-00-00' && date('Y', strtotime($dtIniRaw)) > 0) ? (int)date('Y', strtotime($dtIniRaw)) : null;
            $anoFim = (!empty($dtFimRaw) && $dtFimRaw !== '0000-00-00' && date('Y', strtotime($dtFimRaw)) > 0) ? (int)date('Y', strtotime($dtFimRaw)) : $anoInicio;
            
            $obrasAnosInicio[] = $anoInicio;
            $obrasAnosFim[] = $anoFim;
            $obrasDetalhes[] = [
                'engenheiro' => $row['engenheiro'],
                'dtInicio' => ($anoInicio !== null) ? date('d/m/Y', strtotime($dtIniRaw)) : '-',
                'dtFim' => ($anoFim !== null) ? date('d/m/Y', strtotime($dtFimRaw)) : '-'
            ];
        }
    }

    // 2. Engenheiros (Linha de Admissão)
    $engLabels = []; $engAnosAdmissao = []; $engDetalhes = [];
    $sqlEng = "SELECT idEngenheiro, dsNome, nrCREA, dtAdmissao FROM engenheiros ORDER BY idEngenheiro ASC";
    $resEng = mysqli_query($conn, $sqlEng);
    if ($resEng) {
        while($row = mysqli_fetch_assoc($resEng)) {
            $engLabels[] = (!empty($row['dsNome']) ? $row['dsNome'] : 'Engenheiro') . " (#" . $row['idEngenheiro'] . ")";
            $dtAdmRaw = $row['dtAdmissao'] ?? '';
            
            $anoAdm = (!empty($dtAdmRaw) && $dtAdmRaw !== '0000-00-00' && date('Y', strtotime($dtAdmRaw)) > 0) ? (int)date('Y', strtotime($dtAdmRaw)) : null;
            $engAnosAdmissao[] = $anoAdm;
            $engDetalhes[] = [
                'crea' => !empty($row['nrCREA']) ? $row['nrCREA'] : '-',
                'dtAdmissao' => ($anoAdm !== null) ? date('d/m/Y', strtotime($dtAdmRaw)) : '-'
            ];
        }
    }

    // 3. Orçamentos (Status)
    $totalAprovados = 0; 
    $totalPendentes = 0;
    
    $resOrcAprov = mysqli_query($conn, "SELECT COUNT(*) as total FROM orcamentos WHERE dtAprovacao IS NOT NULL AND YEAR(dtAprovacao) > 0");
    if ($resOrcAprov && $row = mysqli_fetch_assoc($resOrcAprov)) { 
        $totalAprovados = (int)$row['total']; 
    }
    
    $resOrcPend = mysqli_query($conn, "SELECT COUNT(*) as total FROM orcamentos WHERE dtAprovacao IS NULL OR YEAR(dtAprovacao) = 0");
    if ($resOrcPend && $row = mysqli_fetch_assoc($resOrcPend)) { 
        $totalPendentes = (int)$row['total']; 
    }

    // 4. Fases
    $fasesLabels = []; $fasesIds = []; $fasesDetalhes = [];
    $sqlFases = "SELECT idFase, dsFase FROM fases ORDER BY idFase ASC";
    $resFases = mysqli_query($conn, $sqlFases);
    if ($resFases) {
        while ($row = mysqli_fetch_assoc($resFases)) {
            $fasesLabels[] = (!empty($row['dsFase']) ? $row['dsFase'] : 'Fase') . " (#" . $row['idFase'] . ")";
            $fasesIds[] = (int)$row['idFase'];
            $fasesDetalhes[] = ['id' => $row['idFase'], 'descricao' => $row['dsFase']];
        }
    }

    return [
        'obras' => ['labels' => $obrasLabels, 'inicio' => $obrasAnosInicio, 'fim' => $obrasAnosFim, 'detalhes' => $obrasDetalhes],
        'engenheiros' => ['labels' => $engLabels, 'admissao' => $engAnosAdmissao, 'detalhes' => $engDetalhes],
        'orcamentos' => ['aprovados' => $totalAprovados, 'pendentes' => $totalPendentes],
        'fases' => ['labels' => $fasesLabels, 'ids' => $fasesIds, 'detalhes' => $fasesDetalhes]
    ];
}

// Resposta para requisição AJAX do polling
if (isset($_GET['api']) && $_GET['api'] == '1') {
    ini_set('display_errors', 0);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(obterDadosDashboard($conn), JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

// Proteção do JSON inicial para evitar quebra do JavaScript
$dadosArray = obterDadosDashboard($conn);
$dadosIniciaisJSON = json_encode($dadosArray, JSON_INVALID_UTF8_SUBSTITUTE);
if (!$dadosIniciaisJSON) {
    $dadosIniciaisJSON = 'null';
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Painel de Gráficos</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #121212; color: #f1f1f1; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { background-color: #1e1e1e; width: 100%; max-width: 900px; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 15px rgba(212, 175, 55, 0.05); border-top: 5px solid #d4af37; }
        h2 { color: #d4af37; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 2px; text-align: center; border-bottom: 1px solid #333333; padding-bottom: 20px; }
        .acoes-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 15px; flex-wrap: wrap; }
        .btn-gray { display: inline-block; padding: 10px 20px; background-color: transparent; color: #a0a0a0; border: 1px solid #555555; border-radius: 6px; text-decoration: none; font-weight: bold; cursor: pointer; transition: all 0.3s ease; whitespace: nowrap; }
        .btn-gray:hover { background-color: #333333; color: #ffffff; border-color: #ffffff; }
        
        /* Estilos do botão de toggle */
        .btn-toggle { padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; border: none; transition: 0.3s; color: white; white-space: nowrap; }
        .btn-toggle.ativo { background-color: #28a745; }
        .btn-toggle.ativo:hover { background-color: #218838; }
        .btn-toggle.inativo { background-color: #dc3545; }
        .btn-toggle.inativo:hover { background-color: #c82333; }

        .select-group { flex-grow: 1; display: flex; justify-content: flex-end; align-items: center; }
        .select-group label { color: #cccccc; font-weight: bold; margin-right: 15px; }
        select { padding: 10px 15px; background-color: #252525; border: 1px solid #d4af37; border-radius: 6px; color: #ffffff; font-size: 1rem; cursor: pointer; outline: none; }
        select:focus { box-shadow: 0 0 8px rgba(212, 175, 55, 0.3); }
        .chart-container { background-color: #252525; padding: 20px; border-radius: 8px; border: 1px solid #333333; height: 450px; position: relative; }
        
        /* Estilos da label de status */
        .status-live { font-size: 11px; color: #28a745; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; margin-top: 10px; text-align: right; transition: color 0.3s; }
        .status-live.inativo { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Dashboard de Informações</h2>
        
        <div class="acoes-top">
            <a href="menu.php" class="btn-gray">← Voltar ao Menu</a>
            
            <button id="btnToggle" class="btn-toggle ativo" onclick="toggleAtualizacao()">Parar Atualização</button>
            
            <div class="select-group">
                <label for="seletorGrafico">Selecione a Área:</label>
                <select id="seletorGrafico">
                    <option value="obras">Gestão de Obras (Linha do Tempo)</option>
                    <option value="engenheiros">Engenheiros (Admissão)</option>
                    <option value="orcamentos">Orçamentos (Status)</option>
                    <option value="fases">Fases da Obra (Gráfico de Linhas)</option>
                </select>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="meuGrafico"></canvas>
        </div>
        <div id="statusLive" class="status-live">● Atualização em Tempo Real Ativa</div>
    </div>

    <script>
        Chart.defaults.color = '#cccccc';
        Chart.defaults.borderColor = '#444444';

        const ctx = document.getElementById('meuGrafico').getContext('2d');
        let chartInstance = null;
        
        // Passagem segura dos dados do PHP para o JavaScript
        let jsonString = '<?= addslashes($dadosIniciaisJSON) ?>';
        let dadosGlobais = jsonString !== 'null' ? JSON.parse(jsonString) : null;
        
        if (!dadosGlobais) {
            alert("Não foi possível carregar os dados. Verifique a consola (F12) para mais detalhes.");
        }

        // Variáveis de controle do Polling
        let intervaloAtualizacao;
        let atualizacaoAtiva = true;

        function renderChart(tipo) {
            if (!dadosGlobais) return;
            if (chartInstance) { chartInstance.destroy(); }
            
            let config;
            
            if (tipo === 'obras') {
                config = {
                    type: 'line',
                    data: {
                        labels: dadosGlobais.obras.labels,
                        datasets: [
                            {
                                label: 'Ano de Início',
                                data: dadosGlobais.obras.inicio,
                                borderColor: '#d4af37',
                                backgroundColor: 'rgba(212, 175, 55, 0.2)',
                                borderWidth: 3, pointRadius: 6, fill: false, tension: 0.2, spanGaps: true
                            },
                            {
                                label: 'Ano de Fim Previsto',
                                data: dadosGlobais.obras.fim,
                                borderColor: '#b30000',
                                backgroundColor: 'rgba(179, 0, 0, 0.2)',
                                borderWidth: 3, pointRadius: 6, fill: false, tension: 0.2, spanGaps: true
                            }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    afterBody: function(ctxTooltip) {
                                        const idx = ctxTooltip[0].dataIndex;
                                        const info = dadosGlobais.obras.detalhes[idx];
                                        return ['Engenheiro: ' + info.engenheiro, 'Início: ' + info.dtInicio, 'Fim: ' + info.dtFim];
                                    }
                                }
                            }
                        },
                        scales: { y: { title: { display: true, text: 'Ano' }, ticks: { precision: 0 } } }
                    }
                };
            } 
            else if (tipo === 'engenheiros') {
                config = {
                    type: 'line',
                    data: {
                        labels: dadosGlobais.engenheiros.labels,
                        datasets: [{
                            label: 'Ano de Admissão',
                            data: dadosGlobais.engenheiros.admissao,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderWidth: 3, pointRadius: 6, fill: true, tension: 0.3, spanGaps: true
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    afterBody: function(ctxTooltip) {
                                        const idx = ctxTooltip[0].dataIndex;
                                        const info = dadosGlobais.engenheiros.detalhes[idx];
                                        return ['CREA: ' + info.crea, 'Data de Admissão: ' + info.dtAdmissao];
                                    }
                                }
                            }
                        },
                        scales: { y: { title: { display: true, text: 'Ano de Admissão' }, ticks: { precision: 0 } } }
                    }
                };
            }
            else if (tipo === 'orcamentos') {
                config = {
                    type: 'doughnut',
                    data: {
                        labels: ['Aprovados', 'Pendentes'],
                        datasets: [{
                            data: [dadosGlobais.orcamentos.aprovados, dadosGlobais.orcamentos.pendentes],
                            backgroundColor: ['rgba(40, 167, 69, 0.8)', 'rgba(220, 53, 69, 0.8)'],
                            borderColor: '#1e1e1e', borderWidth: 2
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                };
            }
            else if (tipo === 'fases') {
                config = {
                    type: 'line',
                    data: {
                        labels: dadosGlobais.fases.labels,
                        datasets: [{
                            label: 'Código da Fase (ID)',
                            data: dadosGlobais.fases.ids,
                            borderColor: 'rgba(153, 102, 255, 1)',
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderWidth: 3, pointRadius: 6, fill: true, tension: 0.2
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    afterBody: function(ctxTooltip) {
                                        const idx = ctxTooltip[0].dataIndex;
                                        const info = dadosGlobais.fases.detalhes[idx];
                                        return ['Descrição: ' + info.descricao];
                                    }
                                }
                            }
                        },
                        scales: { y: { title: { display: true, text: 'Código (#ID)' }, ticks: { precision: 0 } } }
                    }
                };
            }

            chartInstance = new Chart(ctx, config);
        }

        function carregarDadosAtualizados() {
            if (!atualizacaoAtiva) return; 

            fetch('graficos.php?api=1')
                .then(response => response.json())
                .then(data => {
                    dadosGlobais = data;
                    const seletor = document.getElementById('seletorGrafico');
                    renderChart(seletor.value);
                })
                .catch(err => console.log('Sincronizando...'));
        }

        function toggleAtualizacao() {
            const btn = document.getElementById('btnToggle');
            const statusLabel = document.getElementById('statusLive');

            if (atualizacaoAtiva) {
                clearInterval(intervaloAtualizacao);
                atualizacaoAtiva = false;
                
                btn.classList.replace('ativo', 'inativo');
                btn.textContent = 'Iniciar Atualização';
                statusLabel.classList.add('inativo');
                statusLabel.textContent = '○ Atualização em Tempo Real Pausada';
            } else {
                atualizacaoAtiva = true;
                carregarDadosAtualizados(); 
                intervaloAtualizacao = setInterval(carregarDadosAtualizados, 5000);
                
                btn.classList.replace('inativo', 'ativo');
                btn.textContent = 'Parar Atualização';
                statusLabel.classList.remove('inativo');
                statusLabel.textContent = '● Atualização em Tempo Real Ativa';
            }
        }

        document.getElementById('seletorGrafico').addEventListener('change', function() {
            renderChart(this.value);
        });

        renderChart('obras');
        intervaloAtualizacao = setInterval(carregarDadosAtualizados, 5000);
    </script>
</body>
</html>