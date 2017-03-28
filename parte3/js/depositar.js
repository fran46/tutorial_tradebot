var skinsDepositar = [];
var skinsDepositarSuma = 0;
var skinsDepositarTotal = 0;

$(document).ready(function() {
	cargarInventario();
	
	$(document).on("click", "#listadoDeSkins .skin", function() {
		//Obtenemos las propiedades del skin
		var assetid = $(this).data("assetid");
		var nombre = $(this).data("name");
		var imagen = $(this).data("imagen");
		var precio = $(this).data("precio");
		//Mediante indexOf comprobaremos si existe en el array de skins a depositar
		var indexOf = skinsDepositar.indexOf(assetid);
		if(indexOf>=0) {
			//Si existe, lo eliminamos de array y de los items a depositar
			$(this).css("background-color", "#fbfafa");
			skinsDepositar.splice(indexOf, 1);
			$("#listadoDeSkinsDepositar").find("[data-assetid='"+assetid+"']").remove();
			
			skinsDepositarSuma = skinsDepositarSuma-precio;
			skinsDepositarTotal = skinsDepositarTotal-1;
			
			document.getElementById("totalItemsSeleccionados").innerHTML = skinsDepositarTotal;
			document.getElementById("precioItemsSeleccionados").innerHTML = skinsDepositarSuma.toFixed(2);
			
			if(skinsDepositarTotal<=0) { $("#btnDepositar").addClass("hidden"); }
			
		} else {
			//Si no existe, lo añadimos al array y a los items a depositar
			$(this).css("background-color", "#dff0d8");
			skinsDepositar.push(assetid);
			
			skinsDepositarSuma = skinsDepositarSuma+precio;
			skinsDepositarTotal = skinsDepositarTotal+1;
			
			$("#listadoDeSkinsDepositar").append("<div class='skinDeposit' data-assetid='"+assetid+"'><div class='imagen'><img width='80px' src='"+imagen+"'/></div><div class='name'>"+nombre+"</div><div class='precio'>"+precio+" $</div></div>")
			document.getElementById("totalItemsSeleccionados").innerHTML = skinsDepositarTotal;
			document.getElementById("precioItemsSeleccionados").innerHTML = skinsDepositarSuma.toFixed(2);
			
			$("#btnDepositar").removeClass("hidden");
		}
		//listadoDeSkinsDepositar
	});
	
	$(document).on("click", "#btnDepositar", function() {
		alertMensaje("info","Procesando oferta de intercambio...");
		if(skinsDepositar.length==skinsDepositarTotal) {
			var assetsFormateados = "";
			for(var aux=0; aux<skinsDepositar.length; aux++) {
				assetsFormateados += skinsDepositar[aux]+",";
			}
			//console.log(assetsFormateados);
			$.ajax({
				"url": "dispacher.php?pagina=enviarOfertaDepositar",
				type: "GET",
				data: {
					"assetids": assetsFormateados,
				},
				success: function(data) {
					try {
						data = JSON.parse(data);
						console.log(data);
						if(data.success==true) {
							alertMensaje("success", data.mensaje);
						} else {
							alertMensaje("warning", data.mensaje);
						}
					}catch (err) {
						alertMensaje("danger", "Se ha producido un error inesperado!");
					}
				},
				error: function() {
					alertMensaje("danger", "Error al enviar la oferta de intercambio.");
				}
			});
		} else {
			alertMensaje("danger","El numero de elementos añadidos no coincide");
		}
	});
});

function cargarInventario(){
	//Informamos al usuario de que se estan cargando las skins
	alertMensaje("info","Cargando tu inventario...");
	//Generamos el div donde se cargaran las skins
	var div = "<div class='skin' data-name='{0}' data-imagen='{1}' data-pos='{2}' data-assetid='{3}' data-precio='{4}'><div class='imagen'><img width='80px' src='{5}'/></div><div class='name'>{6}</div><div class='precio'>{7} $</div></div>";
	//Llamada ajax que devolvera las skins
	$.ajax({
		"url": "dispacher.php?pagina=depositar",
		success: function(data) {
			try {
                data = JSON.parse(data);
				console.log(data);
				if(data.success==true) {
					alertMensaje("success", data.mensaje);
					//obtengo el total de items
					var totalItems = data.items.length;
					var itemsFormateados = [];
					//recorro tantos items como haya
					for(var num=0; num<totalItems; num++) {
						//creo una variable item con el item correspondiente a la posicion en la que estoy
                        var item = data.items[num];
						var nombre = item.nombre;
                        var imagen = item.img;
						var precio = item.precio;
						var assetid = item.assetid;
                        var itemFormateado = div.format(nombre,imagen,num,assetid,precio,imagen,nombre,precio);
                        itemsFormateados.push(itemFormateado);
                    }
					console.log(itemsFormateados);
					document.getElementById("listadoDeSkins").innerHTML = itemsFormateados.join('');
				} else {
					alertMensaje("warning", data.mensaje);
				}
			} catch(err) {
				alertMensaje("danger", "Se ha producido un error inesperado!");
			}
		},
		error: function() {
            alertMensaje("danger", "Error al obtener tu inventario");
        }
	});
}

function alertMensaje(tipo, mensaje) {
	//Eliminamos los posibles estilos que pueda tener el div
   $("div[role='alert']").removeClass("alert-success alert-danger alert-warning alert-warning hidden");
   //En funcion al tipo de alerta añadimos una u otra clase:
   switch(tipo) {
		case "success":
			$("div[role='alert']").addClass("alert-success").html(mensaje);
			break;
		case "danger":
			$("div[role='alert']").addClass("alert-danger").html(mensaje);
			break;
		case "info":
			$("div[role='alert']").addClass("alert-info").html(mensaje);
			break;
		case "warning":
			$("div[role='alert']").addClass("alert-warning").html(mensaje);
			break;
		default:
			break;
	}
}

if (!String.prototype.format) {
    String.prototype.format = function() {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function(match, number) {
            return typeof args[number] != 'undefined' ?
                args[number] :
                match;
        });
    };
}
