<?php
require_once dirname(__FILE__) . '/private/conf.php';

# Requerir usuarios autenticados
# require dirname(__FILE__) . '/private/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        

        $username = SQLite3::escapeString($username);
        $password = SQLite3::escapeString($password);


        $query = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $username, SQLITE3_TEXT);
        $stmt->bindParam(2, $password, SQLITE3_TEXT);

        $stmt->execute() or die("Invalid query");

        header("Location: list_players.php");
        exit();
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
    <h1>Register</h1>
</header>
<main class="player">
    <form action="#" method="post">
        <label>Username:</label>
        <input type="text" name="username">
        <label>Password:</label>
        <input type="password" name="password">
        <input type="submit" value="Send">
    </form>
    <form action="#" method="post" class="menu-form">
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
