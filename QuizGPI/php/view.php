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
			case 'menu':
				$path = "html/menu.html";
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
			case 'mpgameScreen':
				$path = 'html/mpgame.html';
				break;
			case 'postPartido':
				$path = 'html/postpartido.html';
				break;
            case 'mpPostPartido':
				$path = 'html/mppostpartido.html';
				break;
			case 'ranking':
				$path =  'html/ranking.html';
				break;
			case 'matchmaking':
				$path = 'html/matchmaking.html';
				break;
			default:
				# code...
				break;
		}

		$text = file_get_contents($path) or exit("Error getHtml(".$name.")\npath=".$path);
		return $text;
	}

	/* Carga la página de inicio */
	public static function menu($username)
	{
		// Tener en cuenta sesion de usuario
        $text = View::getHtml("menu");
        $text = str_replace("##USUARIO##", $username, $text); 
		
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
		echo $text;
	}
    
    /* Carga la pantalla del juego multijugador*/
    public static function mpGameScreen($port, $playerID)
    {
		$text = View::getHtml('mpgameScreen');
        $text = str_replace("##PORT##", $port, $text); 
        $text = str_replace("##PLAYERID##", $playerID, $text); 
		echo $text;
	}
    
    /* Carga la pantalla de postpartido Multijugador */
	public static function mpPostPartido($resultado, $puntuacion) { 
		$text = View::getHtml('mpPostPartido');
        if($resultado == '1'){
            $text = str_replace("##RESULTADO##", '¡HAS GANADO!', $text); 
        }else{
            $text = str_replace("##RESULTADO##", 'HAS PERDIDO', $text); 
        }
        
        $text = str_replace("##puntuacion##", $puntuacion, $text); 
		echo $text;
	}
	
	/* Carga la pantalla de postpartido */
	public static function postPartido() { 
		$text = View::getHtml('postPartido');
		return $text;
	}
		
    /* Carga la pagina de login */
    public static function register($errors = null)
    {
        $text = View::getHtml("register");
        $text = View::processMessages($text, $errors);
        echo $text;
    }

	/* Carga la página de ranking */
	public static function ranking($ranking, $infoJugador)
	{
		/* Obtenemos la página */
		$pagina = View::getHtml('ranking');
			
		/* Maquetamos la página */
		$trozos = explode('~~RANK~~', $pagina);
		
		/* Creamos una fila por cada jugador en el ranking */
		$filas = array();
		$posicion = 1;
		foreach($ranking as $rank) {
			$fila = $trozos[1];
			 
			foreach(array('#posicion#' => $posicion, '#jugador#' => $rank['j'], '#puntuacion#' => $rank['p']) as $etiqueta => $reemplazo) { 
					$fila = str_replace($etiqueta, $reemplazo, $fila);
			}
			array_push($filas,$fila);
			$posicion = $posicion + 1;
		}
			
		$trozos[1] = implode('', $filas);
		$pagina = implode('', $trozos);
		
		/* El usuario está entre los 10 primeros? */
		$rank = $infoJugador['rank'];
		$trozos = explode('~~PRANK~~', $pagina);
		if($rank <= count($ranking)) { 
			// Está entre los 10 primeros
			$pagina = $trozos[0] . $trozos[2];
		}
		else { 
			// No está entre los 10 primeros
			$prank = $trozos[1];
			foreach(array('#posicion#' => $infoJugador['rank'], '#jugador#' => $infoJugador['j'], '#puntuacion#' => $infoJugador['p']) as $etiqueta => $reemplazo) { 
					$prank = str_replace($etiqueta, $reemplazo, $prank);
			}
			$pagina = $trozos[0] . $prank . $trozos[2];
		}
		
		$texto = $pagina;
		echo $texto;
	}
	
	/* Carga la página de matchmaking..*/
	public static function matchmaking() 
	{
		$text = View::getHtml('matchmaking');
		echo $text;
	}
}
?>
