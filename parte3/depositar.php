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
    <title>Depositar</title>
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
	<link href="css/style.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="js/depositar.js"></script>
  </head>
  <body style="background-color: #EEE;">
    <div class="container" style="margin-top: 30px; margin-bottom: 30px; padding-bottom: 10px; background-color: #FFF;">
		<h1>Depositar skins</h1>
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
		<div class="alert alert-success hidden" role="alert"></div>
		<div id="listadoDeSkinsDepositar">
			<div id="tituloListadoDeSkinsDepositar">
				<span id="totalItemsSeleccionados">0</span> items seleccionados (<span id="precioItemsSeleccionados">0</span> $) <button id="btnDepositar" type="button" class="btn btn-default hidden">Depositar</button>
			</div>
		
		</div>
		<div id="listadoDeSkins">
		
		</div>
		<?php
		}    
		?>
		<hr>
	</div>
  </body>
</html>
