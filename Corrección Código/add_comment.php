<?php
require_once dirname(__FILE__) . '/private/conf.php';


require dirname(__FILE__) . '/private/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['body']) && isset($_GET['id'])) {
        
        $body = trim($_POST['body']);
        $body = htmlspecialchars($body);

       
        $query = "INSERT INTO comments (playerId, userId, body) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        
        
        $stmt->bindParam(1, $_GET['id'], SQLITE3_INTEGER);
        $stmt->bindParam(2, $_SESSION['userId'], SQLITE3_INTEGER);
        $stmt->bindParam(3, $body, SQLITE3_TEXT);
        
       
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
    <title>Práctica RA3 - Comments creator</title>
</head>
<body>
<header>
    <h1>Comments creator</h1>
</header>
<main class="player">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $_GET['id']; ?>" method="post">
        <h3>Write your comment</h3>
        <textarea name="body"></textarea>
        <input type="submit" value="Send">
    </form>
    <form action="list_players.php" method="post" class="menu-form">
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
