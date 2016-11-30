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
    	$query = "SELECT count(*) n FROM temas WHERE nombre = '".$t."'";
    	$result = DataBase::execute($query);
		$row = mysqli_fetch_array();

		if($row["n"] == 1) // Existe el tema en la bbdd
		{
			// Selecciona n filas aleatorias de la tabla preguntas del tema t
			$query = "SELECT * 
			FROM preguntas 
			WHERE RAND()<(SELECT ((3/COUNT(*))*10) FROM preguntas) and tema = '".$t."' 
			ORDER BY RAND() 
			LIMIT '".$n."'";

			return DataBase::execute($query);
		}
		else // No existe el tema en la bbdd
		{
			return false;
		}
    }
}
?>