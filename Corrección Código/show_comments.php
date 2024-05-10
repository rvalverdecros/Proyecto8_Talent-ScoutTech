<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Práctica RA3 - Comments editor</title>
</head>
<body>
<header>
    <h1>Comments editor</h1>
</header>
<main class="player">

<?php
require_once dirname(__FILE__) . '/private/conf.php';

# Require logged users
# require dirname(__FILE__) . '/private/auth.php'; // No es necesario si es una página de comentarios públicos

# List comments
if (isset($_GET['id'])) {
    $playerId = $_GET['id']; 

    $query = "SELECT C.commentId, U.username, C.body FROM comments C INNER JOIN users U ON C.userId = U.userId WHERE C.playerId = ? ORDER BY C.commentId DESC";
    $stmt = $db->prepare($query);
    $stmt->bindValue(1, $playerId, SQLITE3_INTEGER);
    $result = $stmt->execute() or die("Invalid query: " . $query);

    while ($row = $result->fetchArray()) {
        echo "<div>
                <h4>" . htmlspecialchars($row['username']) . "
                <p>commented: " . htmlspecialchars($row['body']) . "</p> 
              </div>";
    }
}
?>

<div>
    <a href="list_players.php">Back to list</a>
    <a class="black" href="add_comment.php?id=<?php echo isset($playerId) ? $playerId : ''; ?>"> Add comment</a> 

</main>
<footer class="listado">
    <img src="images/logo-iesra-cadiz-color-blanco.png">
    <h4>Puesta en producción segura</h4>
    <a href="http://www.donate.co?amount=100&amp;destination=ACMEScouting/">Please donate</a>
</footer>
</body>
</html>
