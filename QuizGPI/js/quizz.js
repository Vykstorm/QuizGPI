/**
 * 
 * 
 * Este script define la lógica del juego Quizz 
 */ 


$(document).ready(function() { 
	// TODO...
	$("#pantalla_juego").hide() 
	comenzar_juego(function() { 
		$("#resultado").hide();
		
		// Añadimos manejadores a los botones de las respuestas.
		responder = function() { 
			if(responder_pregunta($(this).attr("id"))) { 
				$("#resultado").text("!Respuesta correcta!"); 
			}
			else { 
				$("#resultado").text("Respuesta incorrecta motherfucker");
			}
			$("#resultado").fadeIn(400).delay(1000).fadeOut(400);
			
			setTimeout(function() { 
				if(!siguiente_pregunta()) { 
					$("#r1").unbind();
					$("#r2").unbind();
					$("#r3").unbind();
					$("#r4").unbind();
					$("#pantalla_juego").fadeOut(2000);
					fin_juego(function() { 
						$("#pantalla_juego").hide();
						$("#pantalla_juego").fadeIn(1000);
						$("#ver_ranking").click(function() {
							$("#pantalla_juego").hide();
							ver_ranking(function() { 
								$("#pantalla_juego").fadeIn(500);
								});
							})
						})
				}
			}, 2000);
		}			
		$("#r1").click(responder);
		$("#r2").click(responder);
		$("#r3").click(responder);
		$("#r4").click(responder);
			
		$("#pantalla_juego").fadeIn(1000)
		})
	})
