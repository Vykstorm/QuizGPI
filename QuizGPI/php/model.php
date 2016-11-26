<?php

require_once('bbdd/facade.php'); // Interfaz para acceso a la bbbd
require_once('excel/excel.php');
require_once('session/session.php'); // Manejo de sesiones

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
			if(mysqli_num_rows(Facade::getUser($name, $password))> 0)
            {
                //Set the session variables
                $result = Facade::getUser($name, $password);
				$data = mysqli_fetch_array($result);
				Session::setVar("userID", $data["id"]);
				Session::setVar("userNick", $userNick);
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
    
    public static function registerUser()
    {
    
    }
    
}
?>