<?php

// Modelo y vista
require_once('model.php');
require_once('view.php'); 

require_once('session/session.php'); // Manejo de sesiones

class Controller
{
	/* Metodo principal del controlador */
	/* Recibe un array mapeado ( [op] => "", [id] => "", [ac] => "" )*/
	public static function system($var)
	{
		// Iniciamos la sesion
		Controller::initSession();

		if($var["op"] == "view")
		{
			switch ($var["id"]) 
			{
				case '0':	// Carga el front
					View::front();
					break;
				case '1':   // Carga login
                    View::login();
					break;
                case '2':   // Carga registro
                    View::register();
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
				case 'value':
					
					break;
                    
                case '1':
                    $ret = Model::loginUser();
                    if(empty($ret))
                    {
                        /**/
                    }
                    else
                    {
                        View::login($ret);
                    }
                    break;
                    
                case '2':
                    Model::registerUser();
                    break;
								
				default:
					echo "Error. Controller::system(), op=command.";
					print_r($var);
					break;
			}//END switch ($var["id"]) 

		}// END else if($var["op"] == "command")
		else
			echo "Error. Controller::system()";
			print_r($var);	

	}

	/* Inicio de sesion, es privado porque solo el controlador lo va utilizar */
	private static function initSession()
	{
		Session::init();
	}

	

}

?>