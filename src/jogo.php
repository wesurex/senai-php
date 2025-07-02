<?php
require_once("conexao.php");

$codigoPartida = $_GET['partida_id'] ?? 0;

$sqlInfo = "SELECT rodada_atual, pontuacao FROM partida WHERE id = $codigoPartida";
$resInfo = $conn->query($sqlInfo);
$numeroRodada = 1;
$pontuacaoTotal = 0;

if ($resInfo && $resInfo->num_rows > 0) {
    $dados = $resInfo->fetch_assoc();
    $numeroRodada = $dados['rodada_atual'];
    $pontuacaoTotal = $dados['pontuacao'];
}

$regioesDisponiveis = ['Norte', 'Nordeste', 'Centro-Oeste', 'Sudeste', 'Sul'];
$listaMunicipios = [];

foreach ($regioesDisponiveis as $zona) {
    $sqlZona = "SELECT m.id, m.nome, e.nome AS estado, e.RegiaoNome
                FROM Municipio m
                JOIN Estado e ON m.EstadoId = e.id
                WHERE e.RegiaoNome = '$zona'
                ORDER BY RAND() LIMIT 1";
    $resZona = $conn->query($sqlZona);
    if ($linha = $resZona->fetch_assoc()) {
        $listaMunicipios[] = $linha;
    }
}

$sqlExtra = "SELECT m.id, m.nome, e.nome AS estado, e.RegiaoNome
             FROM Municipio m
             JOIN Estado e ON m.EstadoId = e.id
             ORDER BY RAND() LIMIT 1";

$resExtra = $conn->query($sqlExtra);
if ($linha = $resExtra->fetch_assoc()) {
    $listaMunicipios[] = $linha;
}

shuffle($listaMunicipios);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Rodada <?= $numeroRodada ?> de 10</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #ffe6f0;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 20px;
        }

        .painel {
            margin-bottom: 30px;
        }

        .painel h2 {
            color: #c2185b;
        }

        .grade-jogo {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 150px);
            gap: 12px;
            width: 90%;
            margin: auto;
        }

        .area-regiao {
            border: 2px dashed #f48fb1;
            border-radius: 12px;
            background-color: #fff0f5;
            padding: 10px;
            overflow-y: auto;
        }

        #zona-central {
            grid-column: 2;
            grid-row: 2;
            background-color: #fce4ec;
        }

        #caixa-municipios {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 10px;
        }

        .item-municipio {
            border-radius: 8px;
            padding: 6px 10px;
            cursor: grab;
            font-weight: bold;
            color: #fff;
            background: linear-gradient(to right, #ec407a, #f06292);
            border: none;
        }

        .item-municipio:nth-child(2n) {
            background: linear-gradient(to right, #f48fb1, #f06292);
        }

        button {
            margin-top: 25px;
            padding: 10px 25px;
            font-size: 16px;
            background: #c2185b;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #ad1457;
        }

        #janelaResultado {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        #caixaResultado {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            width: 90%;
            max-width: 420px;
            box-shadow: 0 0 15px #c2185b;
        }

        footer {
            margin-top: 60px;
            font-size: 0.9em;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="painel">
        <h2>Rodada <?= $numeroRodada ?> de 10</h2>
        <p><strong>Pontuação:</strong> <?= $pontuacaoTotal ?> pontos</p>
        <p>⏳ <span id="cronometro">15</span></p>
    </div>

    <div class="grade-jogo">
        <div id="drop-norte" class="area-regiao" ondrop="drop(event)" ondragover="allowDrop(event)">Norte</div>
        <div id="drop-nordeste" class="area-regiao" ondrop="drop(event)" ondragover="allowDrop(event)">Nordeste</div>
        <div id="drop-centro" class="area-regiao" ondrop="drop(event)" ondragover="allowDrop(event)">Centro-Oeste</div>

        <div id="zona-central" class="area-regiao" ondrop="drop(event)" ondragover="allowDrop(event)">
            ???
            <div id="caixa-municipios">
                <?php foreach ($listaMunicipios as $mun): ?>
                    <div class="item-municipio" id="m<?= $mun['id'] ?>" draggable="true" ondragstart="drag(event)" data-regiao="<?= htmlspecialchars($mun['RegiaoNome']) ?>">
                        <?= $mun['nome'] ?> - <?= $mun['estado'] ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="drop-sudeste" class="area-regiao" ondrop="drop(event)" ondragover="allowDrop(event)">Sudeste</div>
        <div id="drop-sul" class="area-regiao" ondrop="drop(event)" ondragover="allowDrop(event)">Sul</div>
    </div>

    <button onclick="enviarRodada()">💾 Salvar</button>

    <div id="janelaResultado">
        <div id="caixaResultado">
            <h2>Resumo da Rodada</h2>
            <p id="resumoTexto"></p>
            <button id="botaoProxima">Próxima Rodada</button>
        </div>
    </div>

    <footer>© 2025 Andressa</footer>

    <script>
        let tempo = 15;
        let contagem = setInterval(() => {
            document.getElementById("cronometro").innerText = tempo;
            if (--tempo < 0) {
                clearInterval(contagem);
                enviarRodada();
            }
        }, 1000);

        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drag(ev) {
            ev.dataTransfer.setData("text", ev.target.id);
        }

        function drop(ev) {
            ev.preventDefault();
            const id = ev.dataTransfer.getData("text");
            const item = document.getElementById(id);
            ev.target.appendChild(item);
        }

        function enviarRodada() {
            clearInterval(contagem);

            const regioes = ['norte', 'nordeste', 'centro', 'sudeste', 'sul'];
            const mapReg = {
                'norte': 'Norte',
                'nordeste': 'Nordeste',
                'centro': 'Centro-Oeste',
                'sudeste': 'Sudeste',
                'sul': 'Sul'
            };

            const respostas = [];
            let acertos = 0;

            regioes.forEach(reg => {
                const area = document.getElementById("drop-" + reg);
                const municipios = area.getElementsByClassName("item-municipio");

                for (let i = 0; i < municipios.length; i++) {
                    const id = municipios[i].id.replace("m", "");
                    const regiaoCorreta = municipios[i].dataset.regiao;
                    const acertou = regiaoCorreta.trim().toLowerCase() === mapReg[reg].toLowerCase();
                    respostas.push({ id_cidade: id, resposta: reg, acertou: acertou ? 1 : 0 });
                    if (acertou) acertos++;
                }
            });

            fetch('salvar_rodada.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    partida_id: <?= $codigoPartida ?>,
                    rodada: <?= $numeroRodada ?>,
                    respostas: respostas
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const modal = document.getElementById("janelaResultado");
                    const texto = document.getElementById("resumoTexto");
                    const botao = document.getElementById("botaoProxima");

                    if (data.rodada_atual >= 10) {
                        texto.innerText = `🎉 Acertos: ${acertos}\nPontuação final: ${data.pontuacao}`;
                        botao.innerText = "Ver Ranking";
                        botao.onclick = () => window.location.href = "index.php";
                    } else {
                        texto.innerText = `✅ Acertos: ${acertos}\n+${acertos * 10} pontos`;
                        botao.innerText = "Próxima Rodada";
                        botao.onclick = () => window.location.href = "jogo.php?partida_id=<?= $codigoPartida ?>";
                    }

                    modal.style.display = "flex";
                } else {
                    alert("Erro ao salvar!");
                }
            });
        }
    </script>
</body>
</html>
