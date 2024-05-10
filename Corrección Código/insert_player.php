<?php
require_once dirname(__FILE__) . '/private/conf.php';

require dirname(__FILE__) . '/private/auth.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;
$name = '';
$team = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name']) && isset($_POST['team'])) {

        $name = trim($_POST['name']);
        $team = trim($_POST['team']);


        if (!empty($id)) {
            $query = "UPDATE players SET name = ?, team = ? WHERE playerid = ?";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $name, SQLITE3_TEXT);
            $stmt->bindParam(2, $team, SQLITE3_TEXT);
            $stmt->bindParam(3, $id, SQLITE3_INTEGER);
        } else {
            $query = "INSERT INTO players (name, team) VALUES (?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $name, SQLITE3_TEXT);
            $stmt->bindParam(2, $team, SQLITE3_TEXT);
        }


        $stmt->execute() or die("Invalid query");
    }
} else {

    if (!empty($id)) {

        $query = "SELECT name, team FROM players WHERE playerid = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $id, SQLITE3_INTEGER);
        $result = $stmt->execute() or die("Invalid query");
        $row = $result->fetchArray(SQLITE3_ASSOC) or die("modifying a nonexistent player!");

        $name = $row['name'];
        $team = $row['team'];
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Práctica RA3 - Players list</title>
</head>
<body>
<header>
    <h1>Player</h1>
</header>
<main class="player">
    <form action="#" method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>"><br>
        <h3>Player name</h3>
        <textarea name="name"><?= htmlspecialchars($name) ?></textarea><br>
        <h3>Team name</h3>
        <textarea name="team"><?= htmlspecialchars($team) ?></textarea><br>
        <input type="submit" value="Send">
    </form>
    <form action="#" method="post" class="menu-form">
        <a href="index.php">Back to home</a>
        <a href="list_players.php">Back to list</a>
        <input type="submit" name="Logout" value="Logout" class="logout">
    </form>
</main>
<footer class="listado">
    <img src="images/logo-iesra-cadiz-color-blanco.png">
    <h4>Puesta en producción segura</h4>
    <a href="http://www.donate.co?amount=100&amp;destination=ACMEScouting/">Please donate</a>
</footer>
</body>
</html>
