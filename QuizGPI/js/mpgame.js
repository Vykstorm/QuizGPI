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
    
    //Handle early disconnects
    window.onbeforeunload = function(e) {
        //Enviar peticion de deconexion temprana
        var msg = {
            type: "disconnect",
            player: $('#gameinfo').attr('data-playerID')
        };
        conn.send(JSON.stringify(msg));
        //return "Has perdido por salir antes de la partida";
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
	
});
