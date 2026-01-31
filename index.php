<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Hangman</title>

<style>
:root {
    --bg: #f5f5f5;
    --card: #ffffff;
    --text: #000000;
}

body.dark {
    --bg: #121212;
    --card: #1e1e1e;
    --text: #f5f5f5;
}

body {
    font-family: Arial, sans-serif;
    background: var(--bg);
    color: var(--text);
    text-align: center;
    transition: 0.3s;
}

.title {
    text-decoration: underline;
    margin-top: 20px;
    color: #2f6fa3;
}

.box {
    width: 350px;
    margin: 20px auto;
    background: var(--card);
    padding: 20px;
    border-radius: 8px;
}

input[type="text"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
}

.radio {
    text-align: left;
}

.start {
    width: 100%;
    padding: 10px;
    background: #2f6fa3;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
}

table {
    margin: 20px auto;
    width: 90%;
    border-collapse: collapse;
    background: var(--card);
}

th {
    background: #2f6fa3;
    color: white;
    padding: 10px;
}

td {
    border: 1px solid #ddd;
    padding: 8px;
}

.toggle {
    position: fixed;
    top: 15px;
    right: 20px;
    cursor: pointer;
    font-size: 22px;
}
</style>
</head>

<body>

<div class="toggle" onclick="toggleDark()">🌙</div>

<h1 class="title">Hangman - Jeu du Pendu</h1>

<div class="box">
<form method="post" action="game.php">
<input type="text" name="username" placeholder="Entrez votre nom" required maxlength="20">

<div class="radio">
<label><input type="radio" name="categorie" value="pays" required> Pays</label><br>
<label><input type="radio" name="categorie" value="animaux"> Animaux</label><br>
<label><input type="radio" name="categorie" value="sport"> Sports</label>
</div>

<input type="submit" value="Commencer le jeu" class="start">
</form>
</div>

<h2>🏆 Meilleurs scores</h2>

<table>
<tr>
<th>#</th><th>Joueur</th><th>Catégorie</th><th>Temps</th><th>Erreurs</th><th>Mot</th><th>Score</th>
</tr>

<?php
$f = __DIR__ . "/scores.txt";

if (file_exists($f)) {
    $lignes = file($f);

    usort($lignes, function($a, $b) {
        $a = explode(";", $a);
        $b = explode(";", $b);
        return ($a[3] <=> $b[3]) ?: ($a[2] <=> $b[2]);
    });

    $i = 1;
    foreach ($lignes as $l) {
        $d = explode(";", trim($l));
        if (count($d) == 6) {
            echo "<tr>
                <td>$i</td>
                <td>".htmlspecialchars($d[0])."</td>
                <td>{$d[1]}</td>
                <td>".gmdate("H:i:s",$d[2])."</td>
                <td>{$d[3]}</td>
                <td>{$d[4]}</td>
                <td>{$d[5]}</td>
            </tr>";
            $i++;
        }
    }
} else {
    echo "<tr><td colspan='7'>Aucun score</td></tr>";
}
?>
</table>

<script>
function toggleDark() {
    document.body.classList.toggle("dark");
    localStorage.setItem("theme",
        document.body.classList.contains("dark") ? "dark" : "light"
    );
}
if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark");
}
</script>

</body>
</html>
