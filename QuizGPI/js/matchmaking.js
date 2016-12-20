/**
 * Este script añade al jugador a la sala de espera
 * para entrar al modo multijugador.
 * Realiza la petición POST: index.php?op=command&id=5&ac=join 
 * para unirse a la cola de jugadores. Se devuelve una respuesta cuando 
 * el jugador haya conseguido unirse a la partida.
 * Después de ello, se redirecciona al usuario a la pantalla multijugador
 * con la petición GET: index.php?op=view&id=9
 */
$(document).ready(function() { 
	// Añadimos al usuario a la sala de espera.
	$.post('/index.php?op=command&id=5&ac=join',
		{},
		function(response) {
			
			// Redireccionar al usuario a la pantalla multijugador
			location.replace('/index.php?op=view&id=9');
		});
});
