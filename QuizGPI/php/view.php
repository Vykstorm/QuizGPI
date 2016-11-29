<?php

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
			case 'postPartido':
				$path = 'html/postpartido.html';
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
    
    /* Carga la pantalla del juego */
    public static function gameScreen()
    {
		$text = View::getHtml('gameScreen');
		return $text;
	}

	/* Carga la pantalla de postpartido */
	public static function postPartido() { 
		$text = View::getHtml('postPartido');
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
