<?php
$steamauth['apikey'] = "A54A31EC87E6EDE9535CBCD995EFED79"; // Introduce tu ApiKey http://steamcommunity.com/dev/apikey
$steamauth['dominio'] = "localhost"; // Introduce tu la URL de tu dominio
$steamauth['logoutpage'] = ""; // Introduce la página a mostrar despues de hacer el login (partiendo desde esta carpeta) por ejemplo "../index.php", o dejalo en blanco "" para redirigir a la página en la que estes
$steamauth['loginpage'] = ""; // Introduce la página a mostrar despues de cerrar sesión (partiendo desde esta carpeta) por ejemplo "../adios.php", o dejalo en blanco "" para redirigir a la página en la que estes
$hash_privado = "vr46_tradebot_tutorial"; // Un texto aleatorio sin espacios"
if (empty($steamauth['apikey'])) {die("<div style='display: block; width: 100%; background-color: red; text-align: center;'>SteamAuth:<br>Please supply an API-Key!</div>");}
if (empty($steamauth['dominio'])) {$steamauth['dominio'] = $_SERVER['SERVER_NAME'];}
if (empty($steamauth['logoutpage'])) {$steamauth['logoutpage'] = $_SERVER['PHP_SELF'];}
if (empty($steamauth['loginpage'])) {$steamauth['loginpage'] = $_SERVER['PHP_SELF'];}
?>

