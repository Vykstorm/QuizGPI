<?php

require_once('bbdd/facade.php'); // Interfaz para acceso a la bbbd
require_once('excel/PHPExcel.php'); // Clase para la generación de las hojas excel

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
	 * Crea una partida nueva (1 jugador).
	 * Inicializa la puntuación del jugador a 0.
	 * Toma como parámetro la ID del usuario.
	 * Devuelve como resultado la id de la nueva partida creada, o false en caso
	 * de error
	 */
	public static function nuevaPartida($user_id) { 
		return Facade::insertPartida(array('j1' => $user_id, 'p1' => 0));
	}
	
	/**
	 * Crea una partida nueva (2 jugadores).
	 * Inicializa la puntuación de ambos jugadores a 0.
	 * Toma como parámetro las IDS de ambos jugadores (en forma de array)
	 * Devuelve como resultado la ID de la nueva partida creada, o false en caso de error.
	 * 
	 */
	public static function nuevaPartidaMultijugador($user_ids) { 
		return Facade::insertPartida(array('j1' => $user_ids[0], 'j2' => $user_ids[1], 'p1' => 0, 'p2' => 0));
	}
	
	/**
	 * Este método incrementa la puntuación del usuario en una partida.
	 * Toma como parámetros la id de la partida y la id del usuario respectivamente
	 * Devuelve true si la puntuación se actualizó correctamente, false en caso contrario
	 */
	public static function actualizarPuntuacion($match_id, $user_id, $puntuacion) { 
		return Facade::actualizarPuntuacion($match_id, $user_id, $puntuacion);
	}
	
	/** 
	 * Este método devuelve información del jugador cuya ID es la indicada.
	 * Devuelve un array con dos elementos:
	 * - j: Es su nombre
	 * - p: Es su puntuación total en el juego
	 * - rank: Es su posición en el ranking de jugadores.
	 * Devuelve false en caso de error o si el jugador no existe.
	 */
	public static function getInfoJugador($user_id) { 
		$result = Facade::getInfoJugador($user_id);
		if(!$result)
			return false;
		return array('rank' => intval($result['posicion']), 'p' => $result['puntuacion'], 'j' => $result['nombre']);
	}


	/**
	 * Este método recive un array en el que cada elemento es otro array que 
	 * contiene la siguiente información:
	 * - j: El nombre de un jugador.
	 * - p: La puntuación del jugador.
	 * Con los datos recividos genera la hoja excel 
	**/
	public static function genRankingSheet($data, $extra)
	{
		// Define la zona horaria
		date_default_timezone_set('Europe/Madrid');

		// Comprueba que el acceso se realiza via HTTP y no CLI
		if(PHP_SAPI == 'cli')
			die('Este archivo solo se puede ver desde un navegador web');

		$objPHPExcel = new PHPExcel();

		// Se asignan las propiedades del libro
		$objPHPExcel->getProperties()->setCreator("QuizApp")
		->setLastModifiedBy("QuizApp")
		->setTitle("Ranking QuizApp") //Título
		->setSubject("Ranking QuizApp") //Asunto
		->setDescription("Ranking QuizApp") //Descripción
		->setKeywords("ranking QuizApp") //Etiquetas
		->setCategory("Excel QuizApp"); //Categoria

		$titleSheet   = "Ranking QuizApp";
		$titleColumns = array('PUESTO','JUGADOR','PUNTUACIÓN');
		$endColumn    = "F";

		// Se combinan las celdas C2 hasta E2 para colocar el título
		$objPHPExcel->setActiveSheetIndex(0)
		->mergeCells('C2:E2');

		//Se agregan los titulos de la hoja, titulo y nombre de columnas
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('C2', $titleSheet) 
		->setCellValue('C4', $titleColumns[0])
		->setCellValue('D4', $titleColumns[1])
		->setCellValue('E4', $titleColumns[2]);	

		// Se itera el array pasado como parámetro y se insertan los dos en la hoja
		$it = 5; // Empezamos desde la línea 5 en la hoja
		$n  = 1; // Para ir metiendo los puestos de cada jugador			

		foreach ($data as $key => $value) {			
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('C'.$it, $n)
			->setCellValue('D'.$it, $value["j"])
			->setCellValue('E'.$it, $value["p"]);		
			$it++;
			$n++;
		}

		// Si el jugador que va a descargar el ranking no aparece en el top n
		// entonces se añade en un campo fuera del ranking su posicion, nombre y 
		// puntuación. Los datos serán pasados por el parámetro $extra

		/*
		if(sizeof($extra) != 0)
		{
			$it += 2;

			// Se combina las celdas para poner el título
			$objPHPExcel->setActiveSheetIndex(0)
			->mergeCells('C'.$it':E'.$it);			

			//Se agregan los titulos de la hoja, titulo y nombre de columnas
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('C'.$it, 'Tu ranking en QuizApp es:') 
			->setCellValue('C'.$it+1, $titleColumns[0])
			->setCellValue('D'.$it+1, $titleColumns[1])
			->setCellValue('E'.$it+1, $titleColumns[2]);

			$it += 2;

			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('C'.$it, $extra["pos"])
			->setCellValue('D'.$it, $extra["j"])
			->setCellValue('E'.$it, $extra["p"]);
		}
		*/
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Ranking QuizApp');

		// Se activa la hoja para que se muestre cuando se abre el archivo
		$objPHPExcel->setActiveSheetIndex(0);

		// Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

		// Se le asigna un nombre por defecto
		header('Content-Disposition: attachment;filename="Ranking QuizApp.xlsx"');

		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}

}
?>
