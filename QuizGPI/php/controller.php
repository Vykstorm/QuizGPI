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
				case '1':   // Carga login
                    View::login();
					break; 
                    
                case '2':   // Carga registro
                    View::register();
                    break;  
                    
                case '3': // Carga juego, pantalla principal
					View::gameScreen();
					break;
				case '8': // Carga jugego, pantalla multijugador
					// TODO
					break;
				case '7': // Carga las preguntas del juego.
					Controller::preguntas();
					break;
				case '4': // Carga juego, pantalla de postpartido
					if (empty($_GET['match_id']) && empty($_POST['match_id'])) { 
						exit('ID de partida no valida');
					}
					$match_id = $_GET['match_id'];
					if (empty($_GET['match_id'])) { 
						$match_id = $_POST['match_id'];
					}
					if (empty(intval($match_id))) { 
						exit('ID de partida no valida');
					}
					$match_id = intval($match_id);
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
					echo "Error. Controller::system(). op=view.";
					print_r($var);
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
                    
				case '3': //Gestionar manejo de usuarios & partidas
					break;
                    
                case '4':
                    Session::destroy();
                    header("Location:index.php?op=view&id=1");
                    break;
                    
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
		$n = 10; // Tamaño máximo del ranking
		$ranking = Model::getRanking($n) or exit('Fallo al obtener el ranking');

		
		/* Mostramos la pagina */
		View::ranking($ranking);
		
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
