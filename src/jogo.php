<?php
require_once("conexao.php");

$partida_id = $_GET['partida_id'] ?? 0;

$sql_partida = "SELECT rodada_atual, pontuacao FROM partida WHERE id = $partida_id";
$res_partida = $conn->query($sql_partida);
$rodada_atual = 1;
$pontuacao = 0;

if ($res_partida && $res_partida->num_rows > 0) {
    $partida = $res_partida->fetch_assoc();
    $rodada_atual = $partida['rodada_atual'];
    $pontuacao = $partida['pontuacao'];
}

$regioes = ['Norte', 'Nordeste', 'Centro-Oeste', 'Sudeste', 'Sul'];
$municipios = [];

foreach ($regioes as $regiao) {
    $sql = "SELECT m.id, m.nome, e.nome AS Estado, e.RegiaoNome
            FROM Municipio m
            JOIN Estado e ON m.EstadoId = e.id
            WHERE e.RegiaoNome = '$regiao'
            ORDER BY RAND() LIMIT 1";

    $res = $conn->query($sql);
    if ($row = $res->fetch_assoc()) {
        $municipios[] = $row;
    }
}

$sql_extra = "SELECT m.id, m.nome, e.nome AS Estado, e.RegiaoNome
              FROM Municipio m
              JOIN Estado e ON m.EstadoId = e.id
              ORDER BY RAND() LIMIT 1";

$res_extra = $conn->query($sql_extra);
if ($row = $res_extra->fetch_assoc()) {
    $municipios[] = $row;
}

shuffle($municipios);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Rodada <?= $rodada_atual ?> de 10</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 150px);
            gap: 10px;
            width: 80%;
            margin: auto;
        }
        .regiao {
            border: 2px dashed #ccc;
            padding: 10px;
            background-color: #f9f9f9;
            overflow-y: auto;
        }
        #drop-inicial {
            grid-column: 2;
            grid-row: 2;
            background-color: #eee;
        }
        #municipios {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-top: 10px;
        }
        .municipio {
            background: white;
            border: 1px solid #ccc;
            padding: 5px;
            cursor: grab;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
        }
        .header {
            margin: 30px 0;
        }
        #resultadoModal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        #resultadoBox {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            max-width: 400px;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Rodada <?= $rodada_atual ?> de 10</h2>
    <p><strong>Pontuação atual:</strong> <?= $pontuacao ?></p>
    <p>⏳ <span id="timer">15</span></p>
</div>

<div class="grid">
    <div id="drop-norte" class="regiao" ondrop="drop(event)" ondragover="allowDrop(event)">Norte</div>
    <div id="drop-nordeste" class="regiao" ondrop="drop(event)" ondragover="allowDrop(event)">Nordeste</div>
    <div id="drop-centro" class="regiao" ondrop="drop(event)" ondragover="allowDrop(event)">Centro-Oeste</div>

    <div id="drop-inicial" class="regiao" ondrop="drop(event)" ondragover="allowDrop(event)">
        ???
        <div id="municipios">
            <?php foreach ($municipios as $m): ?>
                <div class="municipio" id="m<?= $m['id'] ?>" draggable="true" ondragstart="drag(event)"
                    data-regiao="<?= htmlspecialchars($m['RegiaoNome']) ?>">
                    <?= $m['nome'] ?> - <?= $m['Estado'] ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="drop-sudeste" class="regiao" ondrop="drop(event)" ondragover="allowDrop(event)">Sudeste</div>
    <div id="drop-sul" class="regiao" ondrop="drop(event)" ondragover="allowDrop(event)">Sul</div>
</div>

<button onclick="salvar()">💾 SALVAR</button>

<div id="resultadoModal">
    <div id="resultadoBox">
        <h2>Resultado da Rodada</h2>
        <p id="resultadoTexto"></p>
        <button id="proximaRodadaBtn">Próxima Rodada</button>
    </div>
</div>

<p style="margin-top: 50px; color: gray;">© 2025 Wesley</p>

<script>
    let tempo = 15;
    let timer = setInterval(() => {
        document.getElementById("timer").innerText = tempo;
        if (--tempo < 0) {
            clearInterval(timer);
            salvar();
        }
    }, 1000);

    function allowDrop(ev) { ev.preventDefault(); }

    function drag(ev) {
        ev.dataTransfer.setData("text", ev.target.id);
    }

    function drop(ev) {
        ev.preventDefault();
        const id = ev.dataTransfer.getData("text");
        const elem = document.getElementById(id);
        ev.target.appendChild(elem);
    }

    function salvar() {
        clearInterval(timer);
        let acertos = 0;
        const regioes = ['norte', 'nordeste', 'centro', 'sudeste', 'sul'];
        const regiaoMap = {
            'norte': 'Norte',
            'nordeste': 'Nordeste',
            'centro': 'Centro-Oeste',
            'sudeste': 'Sudeste',
            'sul': 'Sul'
        };

        const resultados = [];

        regioes.forEach(reg => {
            const drop = document.getElementById("drop-" + reg);
            const itens = drop.getElementsByClassName("municipio");
            for (let i = 0; i < itens.length; i++) {
                const cidadeId = itens[i].id.replace("m", "");
                const regiaoCidade = itens[i].dataset.regiao;
                const acertou = regiaoCidade.trim().toLowerCase() === regiaoMap[reg].toLowerCase();
                resultados.push({
                    id_cidade: cidadeId,
                    resposta: reg,
                    acertou: acertou ? 1 : 0
                });
                if (acertou) acertos++;
            }
        });

        const pontos = acertos * 10;

        fetch('salvar_rodada.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                partida_id: <?= $partida_id ?>,
                rodada: <?= $rodada_atual ?>,
                respostas: resultados
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const modal = document.getElementById("resultadoModal");
                const texto = document.getElementById("resultadoTexto");
                const botao = document.getElementById("proximaRodadaBtn");

                if (data.rodada_atual >= 10) {
                    texto.innerText = `✅ Você acertou ${acertos} cidade(s)!\n🎯 Pontuação final: ${data.pontuacao} pontos`;
                    botao.innerText = "Ver Ranking";
                    botao.onclick = () => window.location.href = "ranking.php";
                } else {
                    texto.innerText = `✅ Você acertou ${acertos} cidade(s)!\n+${pontos} pontos`;
                    botao.innerText = "Próxima Rodada";
                    botao.onclick = () => window.location.href = "jogo.php?partida_id=<?= $partida_id ?>";
                }

                modal.style.display = "flex";
            } else {
                alert("Erro ao salvar rodada!");
            }
        });
    }
</script>

</body>
</html>
