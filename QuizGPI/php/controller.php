<?php

// Modelo y vista
require_once('model.php');
require_once('view.php'); 
include('session/session.php'); // Manejo de sesiones

class Controller
{
	/* Metodo principal del controlador */
	/* Recibe un array mapeado ( [op] => "", [id] => "", [ac] => "" )*/
	public static function system($var)
	{
		// Iniciamos la sesion
		Controller::initSession();

		// Se define el tamaño del ranking
		$longRanking = 10;
        
        /*    NO EXISTE SESION    */
        if(Controller::checkSession() == false){
            if($var["op"] == "view")
            {
                if($var["id"] == '2')
                {
                    View::register();
                }
                else
                {
                    View::login();
                }
            }
            else if($var["op"] == "command")
            {
                switch ($var["id"]) 
                {
                    case '1': // Login
                        $ret = Model::loginUser();
                        if(empty($ret)) { header("Location:index.php?op=view&id=6"); }
                        else{ View::login($ret); }
                        break; 
                        
                    case '2': // Register
                        $ret = Model::registerUser();
                        if(empty($ret)) { header("Location:index.php?op=view&id=6"); }
                        else{ View::register($ret); }
                        break;
                        
                    default:
                        View::login();
                        break;
                }
            }
            else
            {
                View::login();
            }
            
            return;
        }

        
        /*    EXISTE SESION    */
		if($var["op"] == "view")
		{
			switch ($var["id"]) 
			{
  
                case '3': // Carga juego, pantalla principal (1 jugador)
					// Creamos una nueva partida
					$match_id = Model::nuevaPartida(Session::getVar('userID')) or exit('Fallo al crear la partida');
					
					// Generamos las preguntas que el jugador debe responder
					$tema = 'Informatica'; 
					$num_preguntas = 5;
					$preguntas = Model::getPreguntas($num_preguntas, $tema) or exit('Fallo al generar las preguntas');
					
					// Almacenamos la ID de la partida como variable de sesión.
					Session::setVar('matchID', $match_id);
					// Guardamos información de la partida
					Session::setVar('matchPreguntas', $preguntas); // Preguntas que el usuario deberá responder
					// Mostramos la pantalla del juego.
					View::gameScreen();
					break;
			
				case '8': // Sala de espera para entrar al modo multijugador.
					View::matchmaking();
					break;
				case '9': //  Pantalla MULTIJUGADOR
				
					break;
				case '7': // Carga la SIGUIENTE pregunta del juego.
					if (!Session::getVar('matchID')) { 
						exit('Todavia no ha comenzado la partida!');
					}
					if (empty(Session::getVar('matchPreguntas'))) { 
						header('Content-type: application/json');
						echo json_encode(0); // No quedan más preguntas. 
						break;
					}
				
					$siguiente_pregunta = current(Session::getVar('matchPreguntas'));
					
					// Guardamos un timestamp para luego comprobar el timeout.
					Session::setVar('matchPreguntaTimestamp', $_SERVER['REQUEST_TIME']);
					
					
					// Codificamos la respuesta en formato JSON
					$text = json_encode($siguiente_pregunta);
					
					// Devolvemos la respuesta
					header('Content-type: application/json');
					echo $text; 
					break;
					
				case '4': // Carga juego, pantalla de postpartido
					if(!Session::getVar('matchID')) { 
						exit('Todavia no ha comenzado la partida!');
					}
					if(!empty(Session::getVar('matchPreguntas'))) { 
						exit('Aun quedan preguntas por responder!');
					}
					
					
					// Obtenemos la ID de la partida de la sesión
					$match_id = Session::getVar('matchID');
					
					Controller::postPartido($match_id); 
					break;
					
				
                    
				case '5': // Carga la pagina de ranking
					Controller::ranking();
					break;
             
                    
                case '6':	// Carga el menu
                    $name = Session::getVar('userName');
					View::menu($name);
					break;
                    
				default:
					$name = Session::getVar('userName');
					View::menu($name);
					break;
                    
			}//END switch ($var["id"])

		}// END if($var["op"] == "view")
		else if($var["op"] == "command")
		{
			switch ($var["id"]) 
			{
                case '1': // Login
                    $ret = Model::loginUser();
                    if(empty($ret)) { header("Location:index.php?op=view&id=6"); }
                    else{ View::login($ret); }
                    break;
                    
                case '2': // Register
                    $ret = Model::registerUser();
                    if(empty($ret)) { header("Location:index.php?op=view&id=6"); }
                    else{ View::register($ret); }
                    break;  
                    
				case '3': //Gestionar manejo de usuarios & partidas (1 jugador)
					if (!Session::getVar('matchID')) // Comprobar que el usuario está en una partida.
						break; 
					switch ($var['ac']) {
						case 'answer':	
							/* El usuario responde a la pregunta actual. Nos enviará por POST la respuesta R1, R2, ... */
							
							/* Válidamos los parámetros */
							if(!isset($_POST['answer']) or !in_array($_POST['answer'], array('r1', 'r2', 'r3', 'r4'))) { 
								exit('La respuesta indicada no es valida');
							}
							if(empty(Session::getVar('matchPreguntas'))) { 
								exit('No hay pregunta que responder!');
							}
							
							$respuesta = $_POST['answer'];
							$respuesta = intval(substr($respuesta, 1, 1));
							$pregunta = current(Session::getVar('matchPreguntas'));
							$respuesta_correcta = intval($pregunta['c']);
							
							/* Eliminamos la pregunta actual */
							$preguntas = Session::getVar('matchPreguntas');
							array_shift($preguntas);
							Session::setVar('matchPreguntas', $preguntas);
							
							/* Comprobamos que el timeout de la pregunta no ha expirado */
							$timeout = 20;
							if(($_SERVER['REQUEST_TIME'] - Session::getVar('matchPreguntaTimestamp')) >= $timeout) { 
								// Timeout expirado
								header('Content-type: application/json');
								echo json_encode(1); // Enviar codigo 1
								break;
							}
							/* Si no ha expirado... */
							
							/* Si la respuesta es correcta actualizamos la puntuación del jugador, en base
							 * al tiempo transcurrido desde que se hizo la pregunta */
							// TODO
							if ($respuesta_correcta == $respuesta) { 
								$puntuacion = $timeout - ($_SERVER['REQUEST_TIME'] - Session::getVar('matchPreguntaTimestamp'));
								$user_id = Session::getVar('userID');
								$match_id = Session::getVar('matchID');
								Model::actualizarPuntuacion($match_id, $user_id, $puntuacion) or exit('Fallo al actualizar la puntuacion');
							}
							
							/* Enviar al usuario el código de retorno 0/2 en función de si la respuesta es 
							 * correcta o no */
							header('Content-type: application/json');
							echo json_encode(($respuesta_correcta == $respuesta) ? 2 : 0);
							
							break;
						
						case 'timeout': // El usuario ha alcanzado el timeout de la pregunta...
							if(empty(Session::getVar('matchPreguntas'))) { 
								exit('No hay pregunta que responder!');
							}
													
							/* Eliminamos la pregunta actual */
							$preguntas = Session::getVar('matchPreguntas');
							array_shift($preguntas);
							Session::setVar('matchPreguntas', $preguntas);
							
							/* Enviamos al usuario el código de retorno 1 que indica que el usuario ha alcanzado
							 * el timeout 
							 */
							header('Content-type: application/json');
							echo json_encode(1);
							break;
					}
					break;
                case '5': // Gestión de manejo de usuario & Partidas MULTIJUGADOR
					switch($var['ac']) { 
						case 'join': // Petición para añadir al usuario al matchmaking.
				
							$player = Session::getVar('userID');
													
							$queue = Model::getMultiplayerQueue();
							$queue->lock();
							// Comprobamos si el jugador ya está en cola
							if($queue->estaEnCola($player))
								exit('Ya estas en cola!');

							// Comprobamos si hay algún jugador en la sala de espera
							if(!$queue->estaVacia())
							{
								/* Si hay alguno, eliminar a dicho jugador de la sala de espera
								(le emparejamos con el usuario que inicio esta petición) */
							
								$player2 = $queue->avanzar();
								$queue->unlock();
								
								/* Además, creamos una partida para los dos jugadores */
								$partida = Model::nuevaPartidaMultijugador(array($player, $player2));
							}
							else 
							{
								/* Si no hay nadie, el jugador debe esperar a otro... */
								$queue->encolar($player);
								do
								{
									$queue->unlock();
									sleep(1);
									$queue->lock();
								}while($queue->estaEnCola($player));
								$queue->unlock();
							}					
							break;
					}
					break;
                   
                case '4':
                    Session::destroy();
                    header("Location:index.php?op=view&id=1");
                    break;
                    
                case '5': // Genera la hoja excel del ranking
                	$rank   = Model:getRanking($longRanking);                	
                	$player = Model:getInfoJugador(Session:getVar('userID'));
                	Model:genRankingExcel($rank, $player);
				default:
					echo "Error. Controller::system(), op=command.";
					print_r($var);
					break;
			}//END switch ($var["id"]) 

		}// END else if($var["op"] == "command")
		else
		{
			echo "Error. Controller::system()";
			print_r($var);
		}
	}
	
	
	/* Carga las preguntas del juego */
	/*
	 * El servidor responderá con un texto en formato JSON
	 * con la información sobre las preguntas que el usuario debería
	 * responder.
	 *   
	 */
	public static function preguntas()
	{
		$tema = 'Informatica';
		$num_preguntas = 5;
		/* Obtenemos las preguntas */
		$preguntas = Model::getPreguntas($num_preguntas, $tema) or exit('Fallo al obtener las preguntas: tema=' . $tema . ', num.preguntas=' . $num_preguntas);
		
		$text = json_encode($preguntas);
		
		header('Content-type: application/json');
		echo $text;
	}
	
	/**
	 * Carga la página del ranking de puntuaciones.
	 */
	public static function ranking()
	{
		/* Obtenemos el ranking de puntuaciones */
		//$n = 10; // Tamaño máximo del ranking
		$ranking = Model::getRanking($longRanking) or exit('Fallo al obtener el ranking');

		// Obtenemos las estadísticas del jugador
		$user_id = Session::getVar('userID');
		$infoJugador = Model::getInfoJugador($user_id) or exit('Fallo al obtener los datos del jugador');
		
		/* Mostramos la pagina */
		View::ranking($ranking, $infoJugador);
	}
	
	/**
	 * Carga la página del postpartido.
	 * Toma como parámetro la ID del partido.
	 */
	private static function postPartido($match_id) 
	{
		// Obtenemos la página con la vista de postpartido
		$pagina = View::postPartido();
		
		// Obtenemos la información de la partida.
		$infoPartida = Model::getInfoPartida($match_id) or exit('No es posible obtener la información de partida');
		// Reemplazamos las etiquetas del HTML
		// TODO...
		$pagina = str_replace('##puntuacion##', strval($infoPartida['p1']), $pagina);
		
		// Imprimimos la página
		echo $pagina;
	}

	/* Inicio de sesion, es privado porque solo el controlador lo va utilizar */
	private static function initSession()
	{
		Session::init();
	}
    
    public static function checkSession()
    {
        if(Session::getVar('userID') && Session::getVar('userName'))
        {
            return true;
        }
        else
        {
            return false;
        }
	}

}

?>
