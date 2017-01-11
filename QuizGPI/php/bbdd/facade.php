<?php

require_once('database.php');


/*
*	Clase para la gestionar el acceso a la base de datos
*/

class Facade
{
	
	public static function name_function()
	{
		$query = "";
		$result = DataBase::execute($query);
	}
    
    public static function existsUser($name)
	{
		$query = "SELECT name FROM Usuario WHERE name='".$name."'";  
		$result = DataBase::execute($query);
        return mysqli_num_rows($result)> 0 ? True: False;
	}
    
    public static function getUser($name, $password)
	{
		$query = "SELECT id, name FROM Usuario WHERE name='".$name."' and password='".$password."'";  
		return DataBase::execute($query);
	}
    
    public static function addUser($name, $password)
    {
        $query = "INSERT INTO Usuario (name, password) VALUES ('".$name."', '".$password."')";  
        return DataBase::execute($query);
    }
    
    public static function getUserFromID($id)
	{
		$query = "SELECT name FROM Usuario WHERE id=".$id."";  
		return DataBase::execute($query);
	}
    
    ################ OBTENER INFO DE PREGUNTAS ##################
    
    /* Devuleve n preguntas del tema t*/
    public static function getPreguntas($n, $t)
    {
    	// Comprueba si existe el tema t en la bbdd
    	$query = "SELECT count(*) n FROM Tema WHERE nombre = '".$t."'";
    	$result = DataBase::execute($query);
		$row = mysqli_fetch_array($result);

		if($row["n"] == 1) // Existe el tema en la bbdd
		{
			// Selecciona n filas aleatorias de la tabla preguntas del tema t
			$query = 
			"SELECT * 
			FROM 
				Pregunta 
				INNER JOIN 
					(SELECT id, nombre nombre_tema 
					FROM Tema
					WHERE nombre = '" . $t . "') Tema
				ON Pregunta.tema = Tema.id
			WHERE RAND()<(SELECT ((3/COUNT(*))*10) FROM Pregunta)
			ORDER BY RAND() 
			LIMIT " . $n;
			
			return DataBase::execute($query);
		}
		else // No existe el tema en la bbdd
		{
			return false;
		}
    }
    
    ########################### RANKING #########################
    /**
     * Devuelve el ranking de puntuaciones más altas de los usuarios
     * (solo las primeras n puntuaciones).
     * El resultado de la consulta es un conjunto de filas donde cada una de ellas
     * esta compuesta por dos valores: name, puntuacion 
     * Son el nombre y la puntuación de cada jugador en el ranking.
     * Están ordenadas de mayor a menor puntuación.
     */
    public static function getRanking($n) { 
		$query = 
		'SELECT name, puntuacion
		FROM PuntuacionTotal INNER JOIN Usuario ON (PuntuacionTotal.id = Usuario.id)
		ORDER BY puntuacion DESC, Usuario.id ASC
		LIMIT ' . $n;
		return Database::execute($query);
	}
	
	###################### OBTENER INFO DE PARTIDAS ##########################
	
	private static function getInfoPartidaMultijugador($match_id) { 
		$query = 
		'SELECT Jugador1.name jugador1, Jugador2.name jugador2, Partida.puntuacion1, Partida.puntuacion2
		FROM 
			(SELECT * FROM Partida WHERE id = ' . $match_id . ') Partida
			INNER JOIN (SELECT id, name FROM Usuario) AS Jugador1 ON Partida.usuario1 = Jugador1.id 
			INNER JOIN (SELECT id, name FROM Usuario) AS Jugador2 ON Partida.usuario2 = Jugador2.id';
		return Database::execute($query);
	}
	
	private static function getInfoPartidaIndividual($match_id) { 
		$query = 
		'SELECT name jugador1, puntuacion puntuacion1 
		FROM 
			(SELECT id, usuario1 usuario, puntuacion1 puntuacion FROM Partida WHERE id=' . $match_id .') Puntuacion 
			INNER JOIN 
			(SELECT id, name FROM Usuario) Usuario 
			ON Puntuacion.usuario = Usuario.id';
		return Database::execute($query);
	}
	
	/**
	 * Devuelve como retorno el resultado de una query, que estará compuesta
	 * por una única fila con los siguientes valores (Si la partida fue MULTIJUGADOR):
	 * - jugador1, jugador2 son los nombres de los jugadores que jugaron en la partida
	 * - puntuacion1, puntuacion2 son sus puntuaciones correspondientes
	 * En el caso en el que la partida fuera de un único jugador, se omiten los campos
	 * jugador2 y puntuación2
	 */
	public static function getInfoPartida($match_id) { 
		$query = 'SELECT id FROM Partida WHERE (id = ' . $match_id . ') and (usuario2 is NULL)';
		$result = Database::execute($query);
		if($result) { 
			if(mysqli_fetch_array($result)) { // fue partida de 1 jugador  
				return Facade::getInfoPartidaIndividual($match_id);
			}
			// fue partida de dos jugadores
			return Facade::getInfoPartidaMultijugador($match_id);
		}
		return false;
	}
	
	###################### OBTENER INFO DE JUGADORES ############
	/** 
	 * Este método devuelve información del jugador cuya ID es la indicada.
	 * Devuelve un array con dos elementos:
	 * - nombre: Es su nombre
	 * - puntuacion: Es su puntuación total en el juego
	 * - posicion: Es su posición en el ranking de jugadores.
	 * Devuelve false en caso de error o si el jugador no existe.
	 */
	public static function getInfoJugador($user_id) { 
		$query = 
		'SELECT posicion, name nombre, puntuacion
		FROM 
			(SELECT @curRow := @curRow + 1 AS posicion, id, puntuacion 
			FROM (SELECT * FROM PuntuacionTotal ORDER BY puntuacion DESC) AS Puntuacion
			JOIN    (SELECT @curRow := 1) r) AS Ranking
			INNER JOIN 
			Usuario
			ON Ranking.id = Usuario.id
		WHERE Usuario.id = ' . $user_id;
		$result = Database::execute($query);
		if($result) {
			return mysqli_fetch_array($result); 
		}
		return false;
	}
	
	############## CREACIÓN DE PARTIDAS Y ACTUALIZACIÓN DE PUNTUACIONES #################
	
	/**
	 * Este método incrementa la puntuación del usuario en una partida.
	 * Toma como parámetros la id de la partida y la id del usuario respectivamente
	 * Devuelve true si la puntuación se actualizó correctamente, false en caso contrario
	 */
	public static function actualizarPuntuacion($match_id, $user_id, $puntuacion) { 
		$query = 'UPDATE Partida SET puntuacion1=puntuacion1+' . strval($puntuacion) . ' WHERE id=' . $match_id . ' and usuario1=' . $user_id . ';';
		$query .= 'UPDATE Partida SET puntuacion2=puntuacion2+' . strval($puntuacion) . ' WHERE id=' . $match_id . ' and usuario2=' . $user_id;
		$result = Database::executeMultiQuery($query);
		if($result) { 
			return true;
		}
		return false;
	}

	// Inserta los datos de la partida, tanto de un solo jugador, como de dos
	public static function insertPartida($data)
	{
		// Comprueba si la partida es de un jugador o dos
		if(isset($data["j2"]))
		{
			return Facade::insert2P($data);
		}
		else
		{
			return Facade::insert1P($data);
		}
	}

	// Inserta los datos de una partida de dos jugadores
	private static function insert2P($data)
	{
		$p1 = strval($data["p1"]);
		$p2 = strval($data["p2"]);
		$query = 'INSERT INTO Partida (usuario1,usuario2,puntuacion1,puntuacion2) 
		VALUES ('.$data["j1"].','.$data["j2"].','.$p1.','.$p2.')';
		
		if($id = DataBase::executeInsert($query))
		{
			// Debemos guardar el estado para partidas multijugador.
			$query = 'INSERT INTO EstadoPartidaMultijugador(id, turno_actual) VALUES(' . $id . ', ' . $data['j1'] . ')';
			$result = DataBase::executeInsert($query);
			if(!$result) 
				return false;
			
			return $id;
		}
		else
		{
			return 0;
		}		
	}

	
	//Inserta los datos de una partida de un jugador
	private static function insert1P($data)
	{
		$p1 = strval($data["p1"]);
	
		$query = 'INSERT INTO Partida (usuario1, puntuacion1)
		VALUES ('.$data["j1"].', '.$p1.')';
		if($id = DataBase::executeInsert($query))
		{
			return $id;
		}
		else
		{
			return 0;
		}
	} 
	
	############# MATCHMAKING MULTIPLAYER #############
	/**
	 * Este método bloquea la tabla donde se almacenan las IDs de los usuarios
	 * que están en espera para jugar el modo multijugador de forma que las siguientes
	 * operaciones sobre dicha tabla son atómicas.
	 * Después de bloquear la tabla, no se cierra la conexión a la BD...
	 * Devuelve false en caso de error.
	 */
	public static function lockMultiplayerQueue($mysqli) 
	{
		$query = 'LOCK TABLES MultiPlayerQueue WRITE';
		$result = Database::execute($query, $mysqli, false);
		//$result = true;
		return $result;
	}
	
	/**
	 * Este método desbloquea la tabla donde se almacenan las IDs de los usuarios
	 * que están esperando para jugar el modo multijugador 
	 */
	public static function unlockMultiplayerQueue($mysqli)
	{
		$query = 'UNLOCK TABLES';
		$result = DataBase::execute($query, $mysqli, false);
		//$result = true;
		return $result;
	}
	
	/**
	 * Este método devuelve la ID de usuario del primer jugador que entró en la sala
	 * de espera para jugar el modo multijugador. Devuelve false en caso de error
	 * y null si no hay ningún jugador esperando.
	 */
	 public static function getFirstPlayerWaiting($mysqli)
	 {
		 $query = 'SELECT id FROM MultiPlayerQueue ORDER BY timestamp ASC LIMIT 1';
		 $result = DataBase::execute($query, $mysqli, false);
		 if(!$result) 
			return false;
		 $row = mysqli_fetch_array($result);
		 if(!$row) 
			return null;
		 return $row['id'];
	 }
	 
	 /**
	  * Este método elimina el primer jugador que entró en la sala de espera para jugar
	  * al modo multijugador
	  */
	 public static function popPlayerWaiting($mysqli)
	 {
		 $user_id = Facade::getFirstPlayerWaiting($mysqli);
		 echo mysqli_error($mysqli);
		 if(!$user_id) 
			return false;
		 $query = 'DELETE FROM MultiPlayerQueue WHERE id=' . $user_id;
		 $result = DataBase::execute($query, $mysqli, false);
		 return $result;
	 }
	 
	 /**
	  * Este método añade un jugador a la sala de espera para jugar modo multijugador
	  */
	 public static function pushPlayerWaiting($mysqli, $user_id)
	 {
		 $query = 'INSERT INTO MultiPlayerQueue(id) VALUES(' . $user_id . ')';
		 $result = DataBase::execute($query, $mysqli, false);
		 return $result;
	 }
	 
	 /**
	  * Este método comprueba si un jugador está en espera para jugar el modo multijugador
	  */
	 public static function isPlayerWaiting($mysqli,$user_id)
	 {
		 $query = 'SELECT id FROM MultiPlayerQueue WHERE id = ' . $user_id;
		 $result = DataBase::execute($query, $mysqli, false);
		 if(!$result) 
			return null;
		 if(mysqli_fetch_array($result))
			return true;
		 return false;
	 }
	 
	 #################### MULTIJUGADOR ##########################
	 
}
?>
