<?php
session_start();
include('conexion.db.php');
error_reporting(0);
if(isset($_SESSION['steamid'])) {
	//Si existe la variable de sesion, obtengo los datos del usuario:
	$query_user = mysqli_query($link,"SELECT * FROM usuarios WHERE steamid=".$_SESSION['steamid']);
	if(mysqli_num_rows($query_user)==1) {
		$user = mysqli_fetch_assoc($query_user);		
	} else {
		exit(json_encode(array('success'=>false, 'mensaje'=>'Error conectando a la Base de datos.')));
	}
	if($user["baneado"]==0) {
		
		//Comprobamos que existe la variable pagina
		if(isset($_GET["pagina"])) {
			
			function curl($url) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
				curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 

				$data = curl_exec($ch);
				curl_close($ch);

				return $data;
			}
			
			switch($_GET["pagina"]) {
				
				case "depositar":
				
					$inventario = curl('https://steamcommunity.com/profiles/'.$_SESSION['steamid'].'/inventory/json/730/2/');
					$inventario = json_decode($inventario, true);
					
					$precios = file_get_contents("precios.txt");
					$precios = json_decode($precios, true);
					
					if($inventario['success'] != 1) {
						exit(json_encode(array('success'=> false,'mensaje'=>'Ha ocurrido un error! El API de steam esta caido o inaccesible en estos momentos... o quizas tu inventario es privado, <a href="http://steamcommunity.com/my/edit/settings" target="_blank">comprueba tu inventario</a> e <a href="javascript:cargarInventario()">intentalo nuevamente</a>.')));
					} else {
						$items = array();
						foreach ($inventario['rgInventory'] as $key => $actual) {
							$id = $actual['classid'].'_'.$actual['instanceid'];
							$tradeable = $inventario['rgDescriptions'][$id]['tradable']; //Si es tradeable devuelve 1
							if($tradeable ==1) {
								$nombre = $inventario['rgDescriptions'][$id]['market_hash_name'];
								$precio = $precios[$nombre];
								$img = 'http://steamcommunity-a.akamaihd.net/economy/image/'.$inventario['rgDescriptions'][$id]['icon_url'];
								$items[] = array(
								'img' => $img,
								'nombre' => $nombre,
								'precio' => $precio,
								'assetid' => $actual['id'],
								);
							}
						}
					
						$array = array(
							'items' => $items,
							'success' => true,
							'mensaje' => "Items cargados correctamente"
							);
						exit(json_encode($array));
					}
					break;
				default:
					exit(json_encode(array('success'=>false, 'mensaje'=>'URL no valida')));
					break;
			}
		} else {
			exit(json_encode(array('success'=>false, 'mensaje'=>'Faltan parametros en la URL')));
		}
	} else {
		exit(json_encode(array('success'=>false, 'mensaje'=>'Error! esta baneado')));
	}
} else {
	exit(json_encode(array('success'=>false, 'mensaje'=>'Es necesario iniciar sesión')));
}
?>