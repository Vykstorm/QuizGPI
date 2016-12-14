<?php

require_once('bbdd/facade.php'); // Interfaz para acceso a la bbbd
require_once('excel/excel.php');

/*
*	Clase para gestionar los datos de la aplicación.
*	Se conecta con facade para resolver peticiones a la base de datos
*/

class Model
{
	
    
	public static function loginUser()
    {
    
        //Return information:
        $return = array();
        
		if(isset($_POST["user_login"]) && isset($_POST["user_password"]))
        {
			
            //Clear the input of unwanted characters
			$name = filter_var($_POST["user_login"], FILTER_SANITIZE_STRING);
			$password  = filter_var($_POST["user_password"], FILTER_SANITIZE_STRING);
			
            //Connect with the DB and check if a user with this credentials exists            
			if(mysqli_num_rows(Facade::getUser($name, md5($password)))> 0)
            {
                //Set the session variables
                $result = Facade::getUser($name, md5($password));
				$data = mysqli_fetch_array($result);
                Session::setVar("userID", $data["id"]);
                Session::setVar("userName", $name);
			}
            else 
            {       
				array_push($return, "El usuario o la contraseña son incorrectos.");              
			}

		}else {
			array_push($return, "No se ha proporcionado un nick o una contraseña.");
		}
        
        //Return data
        return $return;

	}
    
    public static function registerUser(){
    
        //Array para guardar los posibles errores que encontremos
        $return = array(); 
    
		if(isset($_POST["user_login"]) && isset($_POST["user_password"]) && isset($_POST["user_password2"])){
            
            // Validar las entradas
			$name  = filter_var($_POST["user_login"],FILTER_SANITIZE_STRING);
			$pwd1 = filter_var($_POST["user_password"],FILTER_SANITIZE_STRING);
			$pwd2  = filter_var($_POST["user_password2"],FILTER_SANITIZE_STRING);
            
            // Comprobar que las contraseñas son iguales
            if($pwd1 != $pwd2){
                array_push($return, "Las contraseñas no coinciden.");
                return $return;
            }

            // Comprobar si el usuario ya existe
			if(Facade::existsUser($name)){
				array_push($return, "Lo sentimos, el nombre de usuario ya existe.");
                return $return;
			}		
            
            // Insertar el nuevo usuario
            if(!Facade::addUser($name, md5($pwd1))){
                array_push($return, "Se ha producido un error con la BBDD");
                return $return;
            }

            // Si todo se ha ejecutado correctamente, configurar sesion
            $result = Facade::getUser($name, md5($pwd1));
            $data = mysqli_fetch_array($result);
            Session::setVar("userID", $data["id"]);
			Session::setVar("userName", $name);          
			
		}else {
			array_push($return, "No se han proporcionado todos los datos necesarios.");
		}		
        
        //Devolvemos los datos y los reportes de errores (si hay)
        return $return;

	}

    /* Devuelve las preguntas para iniciar la partida en un array.
       Como parámetro recive numero de preguntas y el tema. 
       n > 0, t = <un_tema_de_bbdd> <aleatorio> */       
    public static function getPreguntas($n, $t){
    	if($n > 0)
    	{    		
    		if($result = Facade::getPreguntas($n, $t))
    		{
    			$data  = array();

	    		while ($row = mysqli_fetch_array($result))
	    		{
	    			$a = array("id" => $row["id"], "tema" => $row["tema"], 
	    				"p" => $row["pregunta"], "r1" => $row["respuesta1"],
	    				"r2" => $row["respuesta2"], "r3" => $row["respuesta3"],
	    				"r4" => $row["respuesta4"], "c" => $row["correcta"]);

	    			array_push($data, $a);
	    		}

	    		return $data;
    		}
    		else
    		{
    			return false;
    		}
    		
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * Devuelve un array con las n mejores puntuaciones del juego.
     * El valor de retorno debe ser un array. Cada elemento del array es un
     * array asociativo con la siguiente estructura: array('j' => nombre_jugador, 'p' => puntuacion);
     * El array debe estar ordenado en orden decreciente en función de sus puntuaciones.
     * Devuelve false en caso de fallo
     */
    public static function getRanking($n) { 
		$result = Facade::getRanking($n);
		if($result) { 
			$ranking = array(); 
			while($row = mysqli_fetch_array($result))
				array_push($ranking, array('j' => $row['name'], 'p' => intval($row['puntuacion'])));
			return $ranking;
		}
		return false;
	}
	
	/**
	 * Devuelve un array con información sobre los resultados de un partido.
	 * Este array contiene los siguientes campos (si la partida fue entre DOS JUGADORES):
	 * - j1: Nombre del jugador 1
	 * - j2: Nombre del jugador 2 (NULL si la partida fue de 1 solo jugador)
	 * - p1: Puntuación del jugador 1
	 * - p2: Puntuación del jugador 2 (NULL si la partida fue de 1 solo jugador)
	 * Si la partida fue de un único jugador, se omiten los campos j2, p2
	 * Este método toma como parámetro la ID de la partida.
	 * Lanza un error o una excepción en el caso en el que la ID de la partida no sea válida.
	 * Devuelve false en caso de fallo
	 */
	public static function getInfoPartida($match_id) { 
		$result = Facade::getInfoPartida($match_id);
		if($result) { 
			if($row = mysqli_fetch_array($result)) { 
				if (!isset($row['jugador2'])) { // 1 JUGADOR
					return array('j1' => $row['jugador1'], 'p1' => $row['puntuacion1']);
				}
				// 2 JUGADORES
				return array('j1' => $row['jugador1'], 'j2' => $row['jugador2'], 'p1' => intval($row['puntuacion1']), 'p2' => intval($row['puntuacion2']));
			}
		}
		return false;
	}
	
	
	/** 
	 * Almacena los resultados de una partida del juego.
	 * Se debe pasar como parámetro un array con los siguientes campos:
	 * - j1: ID del jugador 1
	 * - p1: Puntuacion del jugador 1
	 * - j2: ID del jugador 2
	 * - p2: Puntuación del jugador 2
	 * Los dos último parámetros se omiten la partida fue de un solo jugador.
	 * Devuelve la id de la partida almacenada o false en caso de fallo
	 */
	public static function guardarResultadosPartida($datos) { 
		$result = Facade::insertPartida($datos);
		if($result)  { 
			$id = $result;
			return $id;
		}
		return false;
	}
}
?>
