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
			
			function obtener_token($tradeurl) {
				parse_str(parse_url($tradeurl, PHP_URL_QUERY), $cadena);
				return isset($cadena['token']) ? $cadena['token'] : false;
			}

			function obtener_partner($tradeurl) {
				parse_str(parse_url($tradeurl, PHP_URL_QUERY), $cadena);
				return isset($cadena['partner']) ? $cadena['partner'] : false;
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
				case "enviarOfertaDepositar":
					//Compruebo si el usuario tiene alguna oferta pendiente de aceptar:
					$qTradesPendientes = mysqli_query($link,"SELECT estado FROM intercambios WHERE usuario = ".$_SESSION['steamid']." AND (estado='pendiente' OR estado='enviada')");
					if(mysqli_num_rows($qTradesPendientes)>=1) {
						//Significa que tiene intercambios pendientes
						exit(json_encode(array("success"=>false, "mensaje"=>"Tienes ofertas de intercambio pendientes de aceptar/validar.")));
					} else {
						//Comrpuebo la trade URL del usuario
						$token =  obtener_token($user['trade_url']);
						$partner = obtener_partner($user['trade_url']);
						if($token==false || $partner == false) {
							exit(json_encode(array('success'=>false, 'mensaje'=>'URL de intercambio no valida. Revisa tu perfil.')));
						} else {
							//Compruebo que exista la variable $_GET de assetids
							if(isset($_GET['assetids'])) {
								//Hago un substr para eliminar la ultima ,
								$get_assetids = substr($_GET['assetids'],0,-1);
								//Genero un array con los assetid
								$assetids = explode(',',$get_assetids);
								//Recorro el array y verifico que todos los assetid recibidos son digitos y que ninguno se repite:
								$validacion1 = true;
								for($aux=0;$aux<count($assetids);$aux++) {
									if(count(array_keys($assetids,$assetids[$aux]))>1) {
										$validacion1=false;
									}
									if(!ctype_digit($assetids[$aux])) {
										$validacion1=false;
									}
								}
								if($validacion1==true) {
									
									$numeroItemsDepositar = 0;
									$valueDeposito = 0;
									$validacion2 = true;
									
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
												//$img = 'http://steamcommunity-a.akamaihd.net/economy/image/'.$inventario['rgDescriptions'][$id]['icon_url'];
												$assetid = $actual['id'];
												//Compruebo con los assetids recibidos
												$count = count(array_keys($assetids,$assetid));
												if($count==1) {
													$numeroItemsDepositar++;
													$valueDeposito = $valueDeposito + $precio;
												} else {
													if($count>1) {
														$validacion2 = false;
													}
												}
											}
										}
									}
									
									if($numeroItemsDepositar==count($assetids)) {
										//Insertamos la oferta en base de datos:
										$partner = mysqli_real_escape_string($link,$partner);
										$token = mysqli_real_escape_string($link,$token);
										$secret = rand(11111,99999);
										if(mysqli_query($link,"INSERT INTO intercambios 
										(tipo,
										estado,
										usuario,
										assetids,
										partner,
										token,
										codigo_secreto,
										value,
										tradeoffer_id
										) VALUES
										('deposit',
										'pendiente',
										".$_SESSION['steamid'].",
										'".$get_assetids."',
										'".$partner."',
										'".$token."',
										'".$secret."',
										".number_format($valueDeposito, 2, '.', '').",
										''
										)")) {
											$insertId=mysqli_insert_id($link);
											$out = curl('http://localhost:3001/enviarOfertaDepositar/?intercambio='.$insertId);
											$out = json_decode($out , true);
											if($out!=null) {
												if($out['success'] == true) {
													exit(json_encode(array('success'=>true, 'mensaje'=> $out['mensaje'])));
												} else {
													exit(json_encode(array('success'=>false, 'mensaje'=> $out['mensaje'])));
												}
											} else {
												exit(json_encode(array('success'=>false, 'mensaje'=>'Los bots no se encuentran disponibles en estos momentos. Tu oferta será enviada en los próximos minutos')));
											}
										} else {
											exit(json_encode(array('success'=>false, 'mensaje'=>'Se ha producido un error al generar la oferta de intercambio.')));
										}
									} else {
										exit(json_encode(array('success'=>false, 'mensaje'=>'Items seleccionados no disponibles.')));
									}
									
								} else {
									exit(json_encode(array('success'=>false, 'mensaje'=>'Se ha producido un error al recibir los assetid de las skins. (2)')));
								}
								//hacemos substr para eliminar le ultimo caracter que es un coma:
								exit(json_encode(array('success'=>false, 'mensaje'=>'Los assetsids son: ')));
							} else {
								exit(json_encode(array('success'=>false, 'mensaje'=>'Se ha producido un error al recibir los assetid de las skins. (1)')));
							}
						}
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
