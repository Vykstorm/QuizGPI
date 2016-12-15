/**
 * 
 * 
 * Este script define la lógica del juego Quizz 
 */ 


$(document).ready(function() { 
	// TODO...
	$("#pantalla_juego").hide() 
	
	comenzar_juego(function() { 
		
		/* Función que actualiza la barra del timeout */
		refresh_timeout = function() { 
			porcentaje = tiempo_restante() / answer_timeout;
			progreso = $("#timeout_clock").data('progress');
			progreso.set(Math.min(Math.max(porcentaje*100, 0), 100));
		}
		
		refresh_bar = setInterval(refresh_timeout, answer_timeout);
		
		/* Función invocada cuando el servidor ha comprobado si la respuesta
		es correcta o no. */
		resultado_obtenido = function(resultado) { 
			if(resultado== 2) { 
				$("#resultado").text("Respuesta correcta");
			}
			else if(resultado==0) { 
				$("#resultado").text("Respuesta incorrecta");
			}
			else if(resultado==1) { 
				$("#resultado").text("Timeout!");
			}

			$("#resultado").fadeIn(400).delay(1000).fadeOut(400);
			
			setTimeout(function() { 
				siguiente_pregunta(function(moreQuestions) {
					$("#c1").click(responder);
					$("#c2").click(responder);
					$("#c3").click(responder);
					$("#c4").click(responder);
					if(!moreQuestions) {  
						$("#c1").unbind();
						$("#c2").unbind();
						$("#c3").unbind();
						$("#c4").unbind();
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
					else { 
						refresh_bar = setInterval(refresh_timeout, answer_timeout);
						}		
					})
				
			}, 2000);
			
		};
				
		// Añadimos manejadores a los botones de las respuestas.
		responder = function() { 
			respuesta = $(this).attr("id").replace("c", "r")
			$("#c1").unbind();
			$("#c2").unbind();
			$("#c3").unbind();
			$("#c4").unbind();
			clearInterval(refresh_bar);
			responder_pregunta(respuesta, resultado_obtenido);

		};			
		
		// Añadimos un manejador para cuando el usuario alcanze el timeout
		answer_timedout = function() { 
			enviar_timeout(function() { 
					resultado_obtenido(1);
				});
		}
		
		$("#c1").click(responder);
		$("#c2").click(responder);
		$("#c3").click(responder);
		$("#c4").click(responder);
		$("#pantalla_juego").fadeIn(1000)
		$("#resultado").hide();
		})
	})
