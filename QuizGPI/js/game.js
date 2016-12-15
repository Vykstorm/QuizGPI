/**
 * Este script gestiona la comunicación cliente-servidor para el juego Quizz.
 * 
 * Notas:
 * En el HTML, debe existir un elemento con la ID "pantalla_juego". Dicho elemento
 * contendrá los elementos de la interfaz de usuario del juego (pantalla del juego principal,
 * pantalla de postpartido, ...)
 * 
 * 
 * 
 * El método comenzar_juego(), se invoca para iniciar el juego; Realiza una petición al servidor para obtener las preguntas que el usuario debe
 * responder.
 * La pantalla del juego principal deberá tener varios elementos HTML que se irán actualizando conforme
 * vaya avanzando el juego:
 * - Elemento HTML con la id "pregunta" que tendrá el contenido de la pregunta (su texto se actualizará cada
 * vez que se le muestre al usuario una nueva pregunta que deba responder.
 * - Elementos HTML con las ids "r1", "r2", "r3", "r4" que serán las posibles opciones a la pregunta (se actualizarán
 * de igual modo que el anterior.
 * 
 * El método responder_pregunta() es invocado cuando el usuario responde a la pregunta actual.
 * Comprueba que la respuesta es correcta o no y actualiza la puntuación del usuario.  
 * 
 * El método siguiente_pregunta() actualiza la pantalla del juego con la siguiente pregunta que 
 * el usuario debe responder.
 * 
 * La función fin_juego() envía al servidor la puntuación del jugador y le redirecciona a la página
 * con la pantalla de postpartido.
 * 
 * El método answer_timedout() es invocada cuando el usuario alcanza el tiempo límite para responder a una pregunta.
 */



$(document).ready(function() { 
	/**** Variables ****/
	answer_timeout = 20; // Indica el número de segundos que tiene el usuario para responder una pregunta.

	/** Esta función carga la siguiente pregunta que el 
	 * usuario debe responder. 
	 * Reemplaza el texto que contiene el elemento HTML con la
	 * etiqueta "pregunta" con el contenido de la pregunta.
	 * Por otro lado, el texto contenido por los elementos HTML
	 * con las ids "r1", "r2", "r3", "r4", son reemplazados por las posibles
	 * respuestas a la pregunta. 
	 * Debe ser invocado después de haber llamado a comenzar_juego()
	 * Devuelve true si hay más preguntas, o false si no las hay
	 * (en cuyo caso, el juego finalizaría -> invocar fin_juego()
	 * 
	 * La siguiente pregunta se carga haciendo la siguiente petición:
	 * GET: /index.php?op=view&id=7
	 * 
	 * Después de realizar dicha petición, 
	 * se invoca el callback que se pasa como argumento (se le pasará como parámetro
	 * un valor booleano indicando si hay más preguntas disponibles  o no
	 */
	siguiente_pregunta = function(callback) {
		$.getJSON('/index.php',
		{op:'view', id:'7'},
		function(response) {
			pregunta = response;
			if (pregunta === 0) { 
				// Que ocurre si no hay más preguntas ?	
				if(jQuery.type(callback) == "function") { 
					callback(false);
				}	
			}
			else { // Hay otra pregunta...	
				$("#pregunta").text(pregunta.p);
				$("#r1").text(pregunta.r1);
				$("#r2").text(pregunta.r2);
				$("#r3").text(pregunta.r3);
				$("#r4").text(pregunta.r4);			
	
				/* Guardamos un timestamp para comprobar posteriormente el
				 * tiempo que dispone el usuario para responder */
				timestamp = new Date().getTime();
				
				timeout = setTimeout(function() { 
						answer_timedout();
					}, answer_timeout*1000);
				
	
				if(jQuery.type(callback) == "function") { 
					callback(true);
				}				
			}
		});
	}
	
	/** Este método es invocado cuando el juego comienza
	 */
	comenzar_juego = function(callback) { 			
		// Imprimimos la primera pregunta
		siguiente_pregunta(callback);
	}
	
	/**
	 * Este método es invocado cuando el usuario responde a la pregunta actual.
	 * Toma como parámetro la respuesta escogida por el mismo, que debe ser
	 * "r1", "r2", "r3" o "r4".
	 * Se comprueba si la respuesta indicada es correcta o no es correcta;
	 * Se envía una petición POST al servidor indicando la respuesta:
	 * POST: index.php?op=command&id=3&ac=answer  y la variable answer por POST (r1, r2, ...)
	 * 
	 * Este método invoca una función con un parámetro cuyo valor será:
	 * - 0 si la respuesta contestada es incorrecta
	 * - 1 si el timeout expiró 
	 * - 2 si la respuesta es correcta
	 */
	responder_pregunta = function(respuesta, callback) { 
		clearTimeout(timeout);
		$.post('/index.php?op=command&id=3&ac=answer',
			{answer:respuesta},
			function(response) {
				resultado = response;
				if(jQuery.type(callback) == "function") { 
					callback(resultado);
				}	
			});
	}
	
	/**
	 * Este método es invocado cuando el usuario supera el timeout de la pregunta.
	 * Envía una petición POST:
	 * index.php?op=command&id=3&ac=timeout 
	 * Este método invoca finalmente el callback pasado como parámetro después de haber
	 * recibido la respuesta del servidor 
	 */
	enviar_timeout = function(callback) { 
		$.post('/index.php?op=command&id=3&ac=timeout',
			{},
			function(response) {
				if(jQuery.type(callback) == "function") { 
					callback();
				}	
			});
	}
	
	/**
	 * Este método devuelve el número de segundos restantes que el usuario tiene para 
	 * responder a la pregunta actual (Solo es válido después de haber invocado el método
	 * siguiente_pregunta(), y de haber recibido la llamada por callback del servidor.
	 */
	tiempo_restante = function() {
		tiempo_transcurrido = ((new Date().getTime() - timestamp) / 1000.0);
		return answer_timeout - tiempo_transcurrido;
	}
	
	
	/**
	 * Este método es invocado cuando el juego finaliza. 
	 * Se redirecciona al jugador, a la página con el postpartido.
	 * La petición es:
	 * /index.php?op=view&id=4
	 * Antes de hacer la redirección, se invoca el callback que se pasa como parámetro.
	 */
	 
	fin_juego = function(callback) { 
		if(jQuery.type(callback) == "function") { 
			callback();
			}	
		location.replace('/index.php?op=view&id=4');
		}
	
});
