<?php

require_once('model.php');

/**
* 	Clase para gestionar las vistas
*/
class View
{
	
    /* Funcion para añadir mensajes de error a una pagina html */
    protected static function processMessages($text, $messages)
    {

        if (strpos($text, '##corteListaErrores##') !== false) {
            $trozos = explode('##corteListaErrores##', $text);
        
            if(is_null($messages) || empty($messages)){
                $text = $trozos[0].$trozos[2];
            }else{
                $aux0 = "";
                for($i=0; $i<count($messages); $i++) {
                    $aux1 = $trozos[1];
                    $aux1 = str_replace("##error##", $messages[$i], $aux1);
                    $aux0 .= $aux1;
                }
                $text = $trozos[0].$aux0.$trozos[2];
            }
        }
        
        return $text;
	}
    
    
	/* Función para cargar un fichero html determinado*/
	protected static function getHtml($name)
	{
		switch ($name) {
			case 'front':
				$path = "html/front.html";
				break;
			case 'menu':
				
				break;
            case 'login':    
                $path = "html/log.html";
                break;
            case 'register':    
                $path = "html/reg.html";
                break;
                
            case 'gameScreen':
				$path = 'html/game.html';
				break;
			default:
				# code...
				break;
		}

		$text = file_get_contents($path) or exit("Error getHtml(".$name.")\npath=".$path);
		return $text;
	}

	/* Carga la página de inicio */
	public static function front()
	{
		// Tener en cuenta sesion de usuario
    	// Cargar datos dinámicamente
		$text = View::getHtml("front");
		echo $text;
	}
    
    /* Carga la pagina de login */
    public static function login($errors = null)
    {
        $text = View::getHtml("login");
        $text = View::processMessages($text, $errors);
        echo $text;
    }
    
	/* Carga la página del juego (Pantalla del juego) */
	/*
	 * El servidor responderá con un texto en formato JSON
	 * con la página HTML de la pantalla principal del juego + 
	 * preguntas que el usuario debe responder
	 *   
	 */
	public static function gameScreen()
	{
		$tema = 'aleatorio';
		$num_preguntas = 5;
		/* Obtenemos las preguntas */
		$preguntas = Model::getPreguntas($num_preguntas, $tema) or exit('Fallo al obtener las preguntas: tema=' . $tema . ', num.preguntas=' . $num_preguntas);
		
		/* Obtenemos la página HTML de la pantalla del juego */
		$pagina = View::getHtml('gameScreen');
		
		/* Devolvemos la respuesta del servidor: preguntas + página en formato JSON */
		$respuesta = array('preguntas' => $preguntas, 'pagina' => $pagina);
		
		
		header('Content-type: application/json');
		$text = json_encode($respuesta);
		echo $text;
	}
    
    /* Carga la pagina de login */
    public static function register()
    {
        $text = View::getHtml("register");
        echo $text;
    }



}
?>
