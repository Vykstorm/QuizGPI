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
     * Devuelve un array con las n mejores puntuaciones del juego.
     * El valor de retorno debe ser un array. Cada elemento del array es un
     * array asociativo con la siguiente estructura: array('j' => nombre_jugador, 'p' => puntuacion);
     * El array debe estar ordenado en orden decreciente en función de sus puntuaciones.
     */
    public static function getRanking($n) { 
		
	}
	
	/**
	 * Devuelve un array con información sobre los resultados de un partido.
	 * Este array contiene los siguientes campos:
	 * - j1: Nombre del jugador 1
	 * - j2: Nombre del jugador 2 (NULL si la partida fue de 1 solo jugador)
	 * - p1: Puntuación del jugador 1
	 * - p2: Puntuación del jugador 2 (NULL si la partida fue de 1 solo jugador)
	 * Este método toma como parámetro la ID de la partida.
	 * Lanza un error o una excepción en el caso en el que la ID de la partida no sea válida.
	 */
	public static function getInfoPartida($match_id) { 
	}
}
?>
