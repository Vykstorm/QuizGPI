
$(document).ready(function() { 
	var preguntas;

	siguiente_pregunta = function() { 
		if (preguntas.len == 0) { 
			// Que ocurre si no hay más preguntas ? 
		}
		else { 
			pregunta = preguntas[preguntas.length-1];
			preguntas.pop();
			
			$("#pregunta").text(pregunta.p);
			$("#r1").text(pregunta.r1);
			$("#r2").text(pregunta.r2);
			$("#r3").text(pregunta.r3);
			$("#r4").text(pregunta.r4);
		}
	}
	
	$("#jugar").click(function() { 
		$.getJSON('/index.php',
			{op:'view', id:'3'},
			function(response) { 
				// Cargamos la pantalla del juego.
				$("#pantalla_juego").append(response.pagina);
				$("#pantalla_juego").append("<button onclick=\"siguiente_pregunta();\">Siguiente pregunta (Para probar)</button>");
				
				preguntas = response.preguntas;
				
				// Imprimimos la primera pregunta
				siguiente_pregunta();
			});
	});
});
