<?php


/**
* 	Clase para la conexion con la base de datos
*/
class DataBase
{	
	// Conexion con la base de datos
	protected static function connect()
	{
		$mysqli = new mysqli("localhost","gpiftp", "pass3232", "gpi");

		if($mysqli->connect_errno)
			echo "Fallo al conectar a MySQL: (".$mysqli->connect_errno.")".$mysqli->connect_error;
			
		// Establecer la codificación de caracteres para las queries
		mysqli_set_charset($mysqli, 'utf8');
	
		return $mysqli;
	}

	// Cierra la conexion con la base de datos
	protected static function close($mysqli)
	{
		mysqli_close($mysqli);
	}

	// Ejecuta las querys
	public static function execute($query)
	{
		$mysqli = DataBase::connect();
		$result = mysqli_query($mysqli, $query);
		DataBase::close($mysqli);
		return $result;
	}
	
	/** Devuelve la última ID del registro insertado por una query de tipo
		INSERT INTO ...
	*/
	public static function getLastID() 
	{
		$mysqli = DataBase::connect();
		$id = mysqli_insert_id($mysqli);
		Database::close($mysqli);
		return $id;
	}
}
?>
