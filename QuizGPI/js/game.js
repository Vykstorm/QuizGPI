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
 */



$(document).ready(function() { 
	var preguntas;


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
	 */
	siguiente_pregunta = function() {
		if (preguntas.length == 0) { 
			// Que ocurre si no hay más preguntas ?
			return false
		}
		else { 
			pregunta = preguntas[preguntas.length-1];
			
			$("#pregunta").text(pregunta.p);
			$("#r1").text(pregunta.r1);
			$("#r2").text(pregunta.r2);
			$("#r3").text(pregunta.r3);
			$("#r4").text(pregunta.r4);
			
			return true
		}
	}
	
	
	/** Este método realiza una petición GET al servidor para
	 * btener las preguntas que el usuario debe responder.
	 * La petición GET es: /index.php?op=view&id=7
	 * Una vez que la petición haya sido respondida con exito (se han obtenido las
	 * preguntas), se invoca el callback que se pasa como parámetro)
	 */
	comenzar_juego = function(callback) { 
		$.getJSON('/index.php',
			{op:'view', id:'7'},
			function(response) { 								
				preguntas = response;
				
				// Imprimimos la primera pregunta
				siguiente_pregunta();
				if(jQuery.type(callback) == "function") { 
					callback();
				}
				// Inicializamos la puntuación de usuario
				puntuacion = 0
			});
	}
	
	/**
	 * Este método es invocado cuando el usuario responde a la pregunta actual.
	 * Toma como parámetro la respuesta escogida por el mismo, que debe ser
	 * "r1", "r2", "r3" o "r4".
	 * Se comprueba si la respuesta indicada es correcta o no es correcta.
	 * Se actualiza la puntuación del usuario 
	 * en base a cuanto tiempo a transcurrido desde que se le mostró inicialmente la pregunta.
	 * Devuelve un valor booleano indicando si la respuesta es correcta o no.
	 * Nota: Esta función debe invocarse después de haber llamado a siguiente_pregunta().
	 */
	responder_pregunta = function(respuesta) { 
		pregunta = preguntas[preguntas.length-1];
		preguntas.pop();
		if(pregunta.c == respuesta) { 
			// Añadir puntuación al usuario.
			tiempo_restante = 5; // Tiempo transcurrido en segundos (siempre > 0)
			puntuacion = puntuacion + Math.ceil(tiempo_restante); // Actualizar puntuación
			
			return true
		}
		else { 
			// Como actualizamos la puntuación si no respondemos a la pregunta ??
			// TODO
			// puntuacion = 
			return false
		}
	}
	
	
	/**
	 * Este método es invocado cuando el juego finaliza. 
	 * Envía la puntuación final al servidor(haciendo una petición POST. 
	 * La petición POST es /index.php?op=command&id=3&ac=actualizar_puntuacion. Se pasa como parámetro via POST la puntuación obtenida. 
	 * El resultado de esta petición POST, es la ID de la partida que el usuario acaba de jugar.
	 * Por último se redirecciona al jugador, a la página:
	 * /index.php?op=view&id=4&match_id=<id_de_la_partida>
	 * Antes de hacer la redirección, se invoca el callback que se pasa como parámetro.
	 */
	fin_juego = function(callback) { 
		$.post('/index.php?op=command&id=3&ac=actualizar_puntuacion', 
			{puntuacion:puntuacion},
			function(response) { 
				if(jQuery.type(callback) == "function") { 
					callback();
				}
				match_id = response;
				location.replace('/index.php?op=view&id=4&match_id=' + match_id);
			})
	}
	
});
