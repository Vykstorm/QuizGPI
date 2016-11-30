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
                $result = Facade::getUser($name, $password);
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
            $result = Facade::getUser($name, $pwd1);
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
    			$data   = array();

	    		while ($row = mysqli_fetch_array($result))
	    		{
	    			$a = array("id" => $row["id"], "tema" => $row["tema"], 
	    				"p" => $row["pregunta"], "r1" => $row["respuesta1"],
	    				"r2" => $row["respuesta2"], "r3" => $row["respuesta3"],
	    				"r4" => $row["respuesta4"], "c" => $row["correcta"]);

	    			$data = array_push($data, $a);
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
     */
    public static function getRanking($n) { 
		return array();
	}
}
?>
