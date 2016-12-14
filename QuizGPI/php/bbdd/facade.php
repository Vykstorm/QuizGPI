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
		ORDER BY puntuacion DESC
		LIMIT ' . $n;
		return Database::execute($query);
	}
	
	
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
	 * Devuelve como resultado el resultado de una query, que estará compuesta
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
		if(DataBase::execute($query))
		{
			return DataBase::getLastId();
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
		if(DataBase::execute($query))
		{
			return DataBase::getLastId();
		}
		else
		{
			return 0;
		}
	} 
}
?>
