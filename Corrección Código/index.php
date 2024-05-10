<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Logout'])) {

    setcookie('user', '', time() - 3600); 
    setcookie('password', '', time() - 3600);
    setcookie('userId', '', time() - 3600);

    unset($_COOKIE['user']);
    unset($_COOKIE['password']);
    unset($_COOKIE['userId']);

    header("Location: index.php");
    exit();
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
    <title>Práctica RA3</title>
</head>
<body>
    <header>
        <h1>Developers Awards</h1>
    </header>
    <main>
        <h2><a href="insert_player.php"> Añadir un nuevo jugador</a></h2>
        <h2><a href="list_players.php"> Lista de jugadores</a></h2>
        <h2><a href="buscador.html"> Buscar un jugador</a></h2>
    </main>
    <form action="#" method="post" class="menu-form">
        <input type="submit" name="Logout" value="Cerrar sesión" class="logout">
    </form>
    <footer>
        <h4>Puesta en producción segura</h4>
        <a href="http://www.donate.co?amount=100&amp;destination=ACMEScouting/">Por favor, dona</a>
    </footer>
</body>
</html>
