<?php
// 🔹 Activer l'affichage des erreurs pendant le développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// 🔹 Fonction pour initialiser la partie
function initialiserPartie() {
    if (!isset($_POST['username'], $_POST['categorie'])) {
        die("Erreur : formulaire incomplet !");
    }

    $_SESSION['username'] = htmlspecialchars($_POST['username']);
    $_SESSION['categorie'] = $_POST['categorie'];
    $_SESSION['erreurs'] = 0;
    $_SESSION['lettres'] = [];
    $_SESSION['start'] = time();

    $fichiers = [
        "sport" => __DIR__."/BD/sport.txt",
        "animaux" => __DIR__."/BD/animal.txt",
        "pays" => __DIR__."/BD/country.txt"
    ];

    if (!isset($fichiers[$_SESSION['categorie']])) {
        die("Erreur : catégorie invalide !");
    }

    if (!file_exists($fichiers[$_SESSION['categorie']])) {
        die("Erreur : fichier de mots introuvable !");
    }

    $mots = file($fichiers[$_SESSION['categorie']], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$mots) die("Erreur : impossible de lire les mots !");
    
    $_SESSION['mot'] = strtoupper(trim($mots[array_rand($mots)]));
    $_SESSION['masque'] = str_repeat("_", strlen($_SESSION['mot']));
}

// 🔹 Initialiser la partie si nécessaire
if (!isset($_SESSION['mot'])) {
    initialiserPartie();
}

// 🔹 Traitement de la lettre choisie
if (isset($_POST['lettre'])) {
    $l = strtoupper($_POST['lettre']); // mettre en majuscule pour correspondre au mot
    if (!in_array($l, $_SESSION['lettres'])) {
        $_SESSION['lettres'][] = $l;
        $ok = false;

        for ($i = 0; $i < strlen($_SESSION['mot']); $i++) {
            if ($_SESSION['mot'][$i] === $l) {
                $_SESSION['masque'][$i] = $l;
                $ok = true;
            }
        }

        if (!$ok) $_SESSION['erreurs']++;
    }
}

// 🔹 Vérifier la fin de partie
$fin = ($_SESSION['erreurs'] >= 6 || $_SESSION['masque'] === $_SESSION['mot']);

if ($fin && !isset($_SESSION['saved'])) {
    $temps = time() - $_SESSION['start'];
    $score = max(0, 100 - ($_SESSION['erreurs'] * 10) - intdiv($temps, 5));

    file_put_contents(__DIR__ . "/scores.txt",
        $_SESSION['username'].";".$_SESSION['categorie'].";".$temps.";".$_SESSION['erreurs'].";".$_SESSION['mot'].";".$score."\n",
        FILE_APPEND
    );
    $_SESSION['saved'] = true;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Partie en cours</title>
<style>
:root {
    --bg: #f5f5f5;
    --card: #ffffff;
    --text: #000000;
}
body.dark { --bg: #121212; --card: #1e1e1e; --text: #f5f5f5; }
body { font-family: Arial,sans-serif; background: var(--bg); color: var(--text); text-align:center; transition:.3s; }
.info { background: var(--card); width:600px; margin:20px auto; padding:15px; border-radius:8px; }
.word { margin:25px 0; }
.word span { display:inline-block; width:40px; height:40px; line-height:40px; margin:4px; background:#2f6fa3; color:white; font-size:22px; border-radius:6px; font-weight:bold; }
.letters button { width:40px; height:40px; margin:3px; background:#4CAF50; color:white; border:none; border-radius:5px; font-weight:bold; }
.letters button:disabled { background:#ccc; }
.restart { display:inline-block; margin-top:25px; padding:12px 35px; background:#2f6fa3; color:white; text-decoration:none; border-radius:6px; font-size:18px; font-weight:bold; }
.restart:hover { background:#1e5080; }
.toggle { position:fixed; top:15px; right:20px; cursor:pointer; font-size:22px; }
</style>
</head>
<body>

<div class="toggle" onclick="toggleDark()">🌙</div>

<div class="info">
<strong>Joueur :</strong> <?= $_SESSION['username'] ?><br>
<strong>Catégorie :</strong> <?= $_SESSION['categorie'] ?><br>
<strong>Erreurs :</strong> <?= $_SESSION['erreurs'] ?>/6
</div>

<img src="img/Hangman-<?= $_SESSION['erreurs'] ?>.png" width="200">

<div class="word">
<?php foreach (str_split($_SESSION['masque']) as $c) echo "<span>$c</span>"; ?>
</div>

<?php if (!$fin): ?>
<div class="letters">
<form method="post">
<?php foreach (range('A','Z') as $l): ?>
<button name="lettre" value="<?= $l ?>" <?= in_array($l,$_SESSION['lettres'])?'disabled':'' ?>><?= $l ?></button>
<?php endforeach; ?>
</form>
</div>
<?php else: ?>
<h2><?= $_SESSION['masque'] === $_SESSION['mot'] ? "🎉 Vous avez gagné !" : "💀 Vous avez perdu ! Le mot était : ".$_SESSION['mot'] ?></h2>
<a href="index.php" class="restart">🔁 Rejouer</a>
<?php session_destroy(); endif; ?>

<script>
function toggleDark() {
    document.body.classList.toggle("dark");
    localStorage.setItem("theme", document.body.classList.contains("dark") ? "dark" : "light");
}
if (localStorage.getItem("theme") === "dark") document.body.classList.add("dark");
</script>

</body>
</html>
