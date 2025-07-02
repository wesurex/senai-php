<?php
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST["nome_jogadora"]);

    if (!empty($nome)) {
        $verifica = $conn->prepare("SELECT id FROM partida WHERE jogador = ? AND finalizada = 0 LIMIT 1");
        $verifica->bind_param("s", $nome);
        $verifica->execute();
        $res = $verifica->get_result();

        if ($res->num_rows > 0) {
            $id = $res->fetch_assoc()["id"];
        } else {
            $nova = $conn->prepare("INSERT INTO partida (jogador, rodada_atual, pontuacao, finalizada) VALUES (?, 1, 0, 0)");
            $nova->bind_param("s", $nome);
            $nova->execute();
            $id = $nova->insert_id;
        }

        header("Location: jogo.php?partida_id=" . $id);
        exit;
    }
}

$ranking_sql = "SELECT jogador, pontuacao FROM partida WHERE finalizada = 1 AND rodada_atual = 10 ORDER BY pontuacao DESC LIMIT 10";
$ranking_result = $conn->query($ranking_sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>🌸 Jogo das Cidades</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #ffeef5;
            color: #6d214f;
            text-align: center;
            padding: 40px;
        }

        h1 {
            font-size: 2.5em;
            color: #c44569;
            margin-bottom: 10px;
        }

        form {
            margin-top: 30px;
        }

        input[type="text"] {
            padding: 12px;
            font-size: 16px;
            width: 250px;
            border: 2px solid #f8a5c2;
            border-radius: 8px;
            background-color: #fff0f6;
        }

        button {
            padding: 12px 20px;
            margin-left: 10px;
            font-size: 16px;
            background-color: #f78fb3;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: #e667a5;
        }

        h2 {
            margin-top: 50px;
            font-size: 1.8em;
            color: #b33939;
        }

        ol {
            max-width: 400px;
            margin: 20px auto;
            padding: 0;
            list-style-position: inside;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(221, 0, 85, 0.2);
        }

        li {
            padding: 12px;
            border-bottom: 1px solid #fce4ec;
        }

        li:last-child {
            border-bottom: none;
        }

        footer {
            margin-top: 60px;
            color: #b83b5e;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

    <h1>🌸 Jogo das Cidades</h1>

    <form method="post">
        <label for="nome_jogadora"><strong>Informe seu nome:</strong></label><br><br>
        <input type="text" name="nome_jogadora" id="nome_jogadora" required>
        <button type="submit">🎮 Jogar</button>
    </form>

    <h2>🏆 Ranking das Cidades</h2>
    <ol>
        <?php if ($ranking_result->num_rows > 0): ?>
            <?php while ($linha = $ranking_result->fetch_assoc()): ?>
                <li><?= htmlspecialchars($linha["jogador"]) ?> - <?= (int)$linha["pontuacao"] ?> pontos</li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>Ainda não temos partidas finalizadas 🥺</li>
        <?php endif; ?>
    </ol>

    <footer>© 2025 Andressa</footer>

</body>
</html>
