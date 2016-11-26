<?php


/**
* 	Clase para la conexion con la base de datos
*/
class DataBase
{	
	// Conexion con la base de datos
	protected static function connect()
	{
		$mysqli = new mysqli("server","userDB","passwordDB","nameDB");

		if($mysqli->connect_errno)
			echo "Fallo al conectar a MySQL: (".$mysqli->connect_errno.")".$mysqli->connect_error;

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
}
?>