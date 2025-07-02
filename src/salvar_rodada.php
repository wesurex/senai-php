<?php
require_once("conexao.php");

header("Content-Type: application/json");

$payload = json_decode(file_get_contents("php://input"), true);

if (!$payload || !isset($payload['partida_id'], $payload['rodada'], $payload['respostas'])) {
    echo json_encode(["success" => false, "message" => "Dados incompletos."]);
    exit;
}

$idPartida = (int)$payload['partida_id'];
$numeroRodada = (int)$payload['rodada'];
$respostasJogador = $payload['respostas'];
$pontosRodada = 0;

foreach ($respostasJogador as $item) {
    $cidadeId = (int)$item['id_cidade'];
    $respostaDada = $item['resposta'];
    $estaCorreto = (int)$item['acertou'];

    $sqlRodada = $conn->prepare("
        INSERT INTO rodada (id_partida, rodada, id_cidade, resposta, acertou)
        VALUES (?, ?, ?, ?, ?)
    ");
    $sqlRodada->bind_param("iiisi", $idPartida, $numeroRodada, $cidadeId, $respostaDada, $estaCorreto);
    $sqlRodada->execute();
    $sqlRodada->close();

    if ($estaCorreto === 1) {
        $pontosRodada += 10;
    }
}

// Atualiza a tabela da partida com nova pontuação e rodada
$atualiza = $conn->prepare("
    UPDATE partida
    SET pontuacao = pontuacao + ?, rodada_atual = rodada_atual + 1
    WHERE id = ?
");
$atualiza->bind_param("ii", $pontosRodada, $idPartida);
$atualiza->execute();
$atualiza->close();

// Busca o estado atual da partida
$sqlStatus = "SELECT pontuacao, rodada_atual FROM partida WHERE id = $idPartida";
$resStatus = $conn->query($sqlStatus);
$status = $resStatus->fetch_assoc();

echo json_encode([
    "success" => true,
    "pontuacao" => $status['pontuacao'],
    "rodada_atual" => $status['rodada_atual']
]);
