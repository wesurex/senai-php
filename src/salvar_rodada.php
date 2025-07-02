<?php
require_once("conexao.php");

header("Content-Type: application/json");

$dados = json_decode(file_get_contents("php://input"), true);

if (!$dados || !isset($dados['partida_id'], $dados['rodada'], $dados['respostas'])) {
    echo json_encode(["success" => false, "message" => "Dados inválidos."]);
    exit;
}

$partida_id = (int)$dados['partida_id'];
$rodada = (int)$dados['rodada'];
$respostas = $dados['respostas'];
$pontuacao = 0;

foreach ($respostas as $resposta) {
    $id_cidade = (int)$resposta['id_cidade'];
    $resposta_str = $resposta['resposta'];
    $acertou = (int)$resposta['acertou'];

    // Salva no banco
    $stmt = $conn->prepare("INSERT INTO rodada (id_partida, rodada, id_cidade, resposta, acertou)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisi", $partida_id, $rodada, $id_cidade, $resposta_str, $acertou);
    $stmt->execute();
    $stmt->close();

    if ($acertou === 1) {
        $pontuacao += 10;
    }
}

// Atualiza pontuação e rodada
$stmt_update = $conn->prepare("UPDATE partida SET pontuacao = pontuacao + ?, rodada_atual = rodada_atual + 1 WHERE id = ?");
$stmt_update->bind_param("ii", $pontuacao, $partida_id);
$stmt_update->execute();
$stmt_update->close();

// Pega a nova pontuação e rodada atual
$sql = "SELECT pontuacao, rodada_atual FROM partida WHERE id = $partida_id";
$res = $conn->query($sql);
$linha = $res->fetch_assoc();

echo json_encode([
    "success" => true,
    "pontuacao" => $linha['pontuacao'],
    "rodada_atual" => $linha['rodada_atual']
]);
