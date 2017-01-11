$(document).ready(function() { 
	/**** Variables ****/
	answer_timeout = 20; // Indica el número de segundos que tiene el usuario para responder una pregunta.
    sent = 0; //Se ha enviado la respuesta a la ultima pregunta? Pa no enviarlo otra vez en el timeout
    var conn = new WebSocket('ws://94.177.241.148:8081?param='.concat($('#gameinfo').attr('data-playerID')));
    var timeouthandler;
    
    /**** Funciones ****/
    timeoutfn = function(){
        answer_timeout = answer_timeout - 1;
        porcentaje = answer_timeout / 20;
        progreso = $("#timeout_clock").data('progress');
        progreso.set(porcentaje*100);
        if((answer_timeout != 0) && (answer_timeout != 20)){
            timeouthandler = setTimeout(timeoutfn, 1000);
        }else{
            if(sent == 0){
                //Enviar respuesta de timeout
                var msg = {
                    type: "response",
                    text: -1,
                    time: 0,
                    player: $('#gameinfo').attr('data-playerID')
                };
                // Send the msg object as a JSON-formatted string.
                conn.send(JSON.stringify(msg));
                $("#resultado").text("Timeout!");
                $("#resultado").fadeIn(400).delay(1000).fadeOut(400);
            }
        }
    };
    
    
    $('#resultado').hide();

    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log(e.data);
        
        var msg = JSON.parse(e.data);
        
        switch(msg.type) {
            case "playerSet":
                //Actualizar nombres
                $('#n1').text(msg.player1);
                $('#n2').text(msg.player2);
                break;
            
            case "scoreUpdate":          
                //Actualizar puntuaciones
                $('#p1').text(msg.player1);
                $('#p2').text(msg.player2);              
                break;
                
            case "responseUpdate":
                if(sent == 1){
                    $("#resultado").text(msg.message);
                    $("#resultado").fadeIn(400).delay(1000).fadeOut(400);
                }
                break;
                
            case "newQuestion":
            
                $("#pregunta").text(msg.data.p);
				$("#r1").text(msg.data.r1);
				$("#r2").text(msg.data.r2);
				$("#r3").text(msg.data.r3);
				$("#r4").text(msg.data.r4);	
                
                clearTimeout(timeouthandler);
                answer_timeout = 20;
                sent = 0;
                progreso = $("#timeout_clock").data('progress');
                progreso.set(100);
                timeouthandler = setTimeout(timeoutfn, 1000);
                break;
                
            case "endMatch":
                window.location.replace("http://94.177.241.148/index.php?op=view&id=10&ac="+msg.data.win+"-"+msg.data.score);
                break;
        }
    };

    $('#c1').click(function() { 
        //Enviar respuesta
        var msg = {
            type: "response",
            text: 1,
            time: answer_timeout,
            player: $('#gameinfo').attr('data-playerID')
        };

        conn.send(JSON.stringify(msg));
        sent = 1;
    });

    $('#c2').click(function() { 
        //Enviar respuesta
        var msg = {
            type: "response",
            text: 2,
            time: answer_timeout,
            player: $('#gameinfo').attr('data-playerID')
        };

        conn.send(JSON.stringify(msg));
        sent = 1;
    });
    
    $('#c3').click(function() { 
        //Enviar respuesta
        var msg = {
            type: "response",
            text: 3,
            time: answer_timeout,
            player: $('#gameinfo').attr('data-playerID')
        };

        conn.send(JSON.stringify(msg));
        sent = 1;
    });
    
    $('#c4').click(function() { 
        //Enviar respuesta
        var msg = {
            type: "response",
            text: 4,
            time: answer_timeout,
            player: $('#gameinfo').attr('data-playerID')
        };

        conn.send(JSON.stringify(msg));
        sent = 1;
    });
    

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
	 *//*
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
				/*timestamp = new Date().getTime();
				
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
	/*comenzar_juego = function(callback) { 			
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
	/*responder_pregunta = function(respuesta, callback) { 
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
	/*enviar_timeout = function(callback) { 
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
	/*tiempo_restante = function() {
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
	 
	/*fin_juego = function(callback) { 
		if(jQuery.type(callback) == "function") { 
			callback();
			}	
		location.replace('/index.php?op=view&id=4');
		}*/
	
});
