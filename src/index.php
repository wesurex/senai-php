<?php
require_once "conexao.php";

// Jogador clicou no botão JOGAR
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jogador = trim($_POST["jogador"]);

    if (!empty($jogador)) {
        // Verifica se há uma partida incompleta
        $stmt = $conn->prepare("SELECT id FROM partida WHERE jogador = ? AND finalizada = 0 LIMIT 1");
        $stmt->bind_param("s", $jogador);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $partida_id = $row["id"];
        } else {
            // Cria nova partida
            $stmt = $conn->prepare("INSERT INTO partida (jogador, rodada_atual, pontuacao, finalizada) VALUES (?, 1, 0, 0)");
            $stmt->bind_param("s", $jogador);
            $stmt->execute();
            $partida_id = $stmt->insert_id;
        }

        // Redireciona para o jogo
        header("Location: jogo.php?partida_id=" . $partida_id);
        exit;
    }
}

// Ranking apenas de partidas finalizadas (rodada 10)
$sql = "SELECT jogador, pontuacao FROM partida WHERE finalizada = 1 AND rodada_atual = 10 ORDER BY pontuacao DESC LIMIT 10";
$ranking = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>🏠 Jogo das Cidades</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 30px;
            background: #f0f0f0;
        }

        h1 {
            font-size: 2.2em;
        }

        form {
            margin-bottom: 30px;
        }

        input[type="text"] {
            padding: 10px;
            width: 250px;
            font-size: 16px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #218838;
        }

        ol {
            max-width: 400px;
            margin: 0 auto;
            padding: 0;
            list-style-position: inside;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        li {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        li:last-child {
            border-bottom: none;
        }

        footer {
            margin-top: 60px;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>🏠 Jogo das Cidades</h1>

    <form method="post">
        <label for="jogador"><strong>Digite seu nome:</strong></label><br>
        <input type="text" name="jogador" id="jogador" required>
        <button type="submit">🎮 JOGAR</button>
    </form>

    <h2>🏆 Ranking</h2>
    <ol>
        <?php if ($ranking->num_rows > 0): ?>
            <?php while ($row = $ranking->fetch_assoc()): ?>
                <li><?= htmlspecialchars($row["jogador"]) ?> - <?= (int)$row["pontuacao"] ?> pts</li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>Sem partidas finalizadas ainda...</li>
        <?php endif; ?>
    </ol>

    <footer>© 2025 Wesley</footer>
</body>
</html>
