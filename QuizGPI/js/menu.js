/**
 * Este script añade eventos a los botones del menú principal.
 * Debe haber los siguientes elementos:
 * - Un botón con la id "jugar". Este botón nos llevará a la pantalla del
 * juego (1 solo jugador). En este caso, se redirecciona al usuario a la página
 * /index.php?op=view&id=3
 * - Un botón con la id "jugar2". Este botón nos llevará a la pantalla del juego
 * (2 jugadores). En este caso, se redirecciona al usuario a la página
 * /index.php?op=view&id=8
 */

$("#document").ready(function() { 
	$("#jugar").click(function() {
			location.replace('/index.php?op=view&id=3');
		})
	$("#jugar2").click(function() {
			location.replace('/index.php?op=view&id=8');
		})
	$("#ranking").click(function() { 
			location.replace('/index.php?op=view&id=5');
		})
	})

