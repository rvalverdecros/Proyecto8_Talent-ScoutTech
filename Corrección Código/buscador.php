<?php
    require_once dirname(__FILE__) . '/private/conf.php';

    
    require dirname(__FILE__) . '/private/auth.php';

    $name = isset($_GET['name']) ? $_GET['name'] : '';
    $name = SQLite3::escapeString($name); 

    $query = "SELECT playerid, name, team FROM players WHERE name='$name' ORDER BY playerId DESC";

    $stmt = $db->prepare($query); 
    $result = $stmt->execute() or die("Invalid query"); 

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Práctica RA3 - Búsqueda</title>
</head>
<body>
    <header class="listado">
        <h1>Búsqueda de <?php echo htmlspecialchars($name); ?></h1> 
    </header>
    <main class="listado">
        <ul>
        <?php
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) { 
            echo "
                <li>
                <div>
                <span>Name: " . htmlspecialchars($row['name']) . "</span> <!-- Evitar la inyección de HTML -->
                <span>Team: " . htmlspecialchars($row['team']) . "</span> <!-- Evitar la inyección de HTML -->
                </div>
                <div>
                <a href=\"show_comments.php?id=".$row['playerid']."\">(show/add comments)</a> 
                <a href=\"insert_player.php?id=".$row['playerid']."\">(edit player)</a>
                </div>
                </li>\n";
        }
        ?>
        </ul>
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
