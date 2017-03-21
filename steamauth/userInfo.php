<?php
if (empty($_SESSION['steam_uptodate']) or empty($_SESSION['steam_personaname'])) {
	require 'configuracion.php';
	$url = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$steamauth['apikey']."&steamids=".$_SESSION['steamid']); 
	$content = json_decode($url, true);
	$_SESSION['steam_steamid'] = $content['response']['players'][0]['steamid'];
	$_SESSION['steam_personaname'] = strip_tags($content['response']['players'][0]['personaname']);
	$_SESSION['steam_avatar'] = $content['response']['players'][0]['avatar'];
	$_SESSION['steam_avatarmedium'] = $content['response']['players'][0]['avatarmedium'];
	if (isset($content['response']['players'][0]['realname'])) { 
		   $_SESSION['steam_realname'] = $content['response']['players'][0]['realname'];
	   } else {
		   $_SESSION['steam_realname'] = "Real name not given";
	}
	$_SESSION['steam_uptodate'] = time();
	$_hash = md5($_SESSION['steam_steamid']."tradebot_tutorial".rand(1, 500));
	setcookie('hash', $_hash, time() + 3600 * 24 * 7, '/');
	//Compruebo si existe el usuario:
	$check_user = mysqli_query($link,"SELECT steamid, acepta_tos, fecha_registro, hash FROM usuarios WHERE steamid=".$_SESSION['steam_steamid']);
	if(mysqli_num_rows($check_user)==1) {
		mysqli_query($link,"UPDATE usuarios SET realname='".$_SESSION['steam_personaname']."', ip_ultima='".$_SERVER['REMOTE_ADDR']."', hash='".$_hash."' WHERE steamid=".$_SESSION['steam_steamid']);
		$row_check_user = mysqli_fetch_array($check_user);
		$_SESSION["acepta_tos"]=$row_check_user[1];
		$_SESSION["fecha_registro"]=$row_check_user[2];
	} else {
		//Si no existe el usuario lo creo
		mysqli_query($link,"INSERT INTO usuarios (steamid,realname,avatar,avatarmedium,fecha_registro,ip_registro,ip_ultima,hash) VALUES 
		(".$_SESSION['steam_steamid'].",'".$_SESSION['steam_personaname']."','".$_SESSION['steam_avatar']."','".$_SESSION['steam_avatarmedium']."','".date("Y-m-d")."','".$_SERVER['REMOTE_ADDR']."','".$_SERVER['REMOTE_ADDR']."','".$_hash."')");
	}
}
?>
