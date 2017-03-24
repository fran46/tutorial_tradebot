$(document).ready(function() {
	cargarInventario();
});

function cargarInventario(){
	//Informamos al usuario de que se estan cargando las skins
	alertMensaje("info","Cargando tu inventario...");
	//Generamos el div donde se cargaran las skins
	var div = "<div class='skin' data-name='{0}' data-imagen='{1}' data-pos='{2}'><div class='imagen'><img width='80px' src='{3}'/></div><div class='name'>{4}</div><div class='precio'>{5} $</div></div>";
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
        
                        var itemFormateado = div.format(nombre,imagen,num,imagen,nombre,precio);
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