<?php
	include('conexion.db.php');	
    require('steamauth/steamauth.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Steam Login PHP</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .table {
            table-layout: fixed;
            word-wrap: break-word;
        }
    </style>
  </head>
  <body style="background-color: #EEE;">
    <div class="container" style="margin-top: 30px; margin-bottom: 30px; padding-bottom: 10px; background-color: #FFF;">
		<h1>Inicio</h1>
		<hr>
		<?php
		//Si no existe la variable de sesion "steamid" mostramos el boton de inicio de sesion
		if(!isset($_SESSION['steamid'])) {
			echo "<div style='margin: 30px auto; text-align: center;'>Inicia sesión<br>";
			loginbutton();
			echo "</div>";
		}  else {
			//Si ya existe la variable de sesion...
			include ('steamauth/userInfo.php'); //
		?>	
		<br>
		<h4 style='margin-bottom: 3px; float:left;'>Datos:</h4><span style='float:right;'><?php logoutbutton(); ?></span>
		<table class='table table-striped'>
			<tr>
				<td><b>Variable</b></td>
				<td><b>Valor</b></td>
				<td><b>Descripción</b></td>
			</tr>
			<tr>
				<td>$_SESSION['steam_steamid']</td>
				<td><?php echo $_SESSION['steam_steamid']; ?></td>
				<td>SteamID64 del usuario</td>
			</tr>
			<tr>
				<td>$_SESSION['steam_personaname']</td>
				<td><?php echo $_SESSION['steam_personaname']; ?></td>
				<td>Nombre del usuario</td>
			</tr>
			<tr>
				<td>$_SESSION['steam_avatar']</td>
				<td><img src="<?php echo $_SESSION['steam_avatar']; ?>"/></td>
				<td>Avatar 32x32</td>
			</tr>
			<tr>
				<td>$_SESSION['steam_avatarmedium']</td>
				<td><img src="<?php echo $_SESSION['steam_avatarmedium']; ?>"/></td>
				<td>Avatar 64x64</td>
			</tr>
			<tr>
				<td>$_SESSION['acepta_tos']</td>
				<td><?php echo $_SESSION['acepta_tos']; ?></td>
				<td>Aceptacion de TOS (Terminos del servicio)</td>
			</tr>
			<tr>
				<td>$_SESSION['fecha_registro']</td>
				<td><?php echo $_SESSION['fecha_registro']; ?></td>
				<td>Fecha de registro</td>
			</tr>
			<tr>
				<td>$_SESSION['baneado']</td>
				<td><?php echo $_SESSION['baneado']; ?></td>
				<td>Baneado (0 = no, 1 = si)</td>
			</tr>
		</table>
		<h4 style='margin-bottom: 3px;'>Obtencion datos de BD:</h4>
		<table class='table table-striped'>
			<tr>
				<td><b>SQL</b></td>
				<td><b>Resultado</b></td>
			</tr>
			<tr>
				<td><?php echo "SELECT balance, retirada_disponible, total_depositado, total_retirado 
				FROM usuarios WHERE steamid=".$_SESSION['steamid']; ?></td>
				<td><?php 
				$query = mysqli_query($link,"SELECT balance, retirada_disponible, total_depositado, total_retirado 
				FROM usuarios WHERE steamid=".$_SESSION['steamid']);
				if(mysqli_num_rows($query)==1) {
					$row = mysqli_fetch_assoc($query);
					echo "Balance: ".$row['balance']."<br/>
					Retirada disponible: ".$row['retirada_disponible']."<br/>
					Total depositado: ".$row['total_depositado']."<br/>
					Total retirado: ".$row['total_retirado'];
				}
				?></td>
			</tr>
		</table>
		<h4 style='margin-bottom: 3px;'>Otras paginas:</h4>
		<a href="depositar.php">Depositar skins</a><br/>
		<a href="retirar.php">Retirar skins</a>
		<?php
		}    
		?>
		<hr>
	</div>
  </body>
</html>
