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
	
	/** Ejecuta una query. La diferencia con respecto al método 
	 * execute() es que este devuelve la ID de la nueva fila insertada en 
	 * la tabla en el caso de que la consulta fueste del tipo INSERT INTO... 
	 * Devuelve false si la consulta fallo
	 */
	public static function executeInsert($query)
	{
		$mysqli = DataBase::connect();
		$result = mysqli_query($mysqli, $query);
		$id = mysqli_insert_id($mysqli);
		DataBase::close($mysqli);
		return $result ? $id : false;
	} 
	
	/**
	 * Ejecuta multiples queries separadas por ;
	 * Devuelve false en caso de que alguna de estas fallará. True en caso
	 * contrario.
	 * En caso de que alguna de las queries falle, se haría un rollback del resto
	 * (si se hace un insert o un update, y luego una query falla, se revierten
	 * los cambios 
	 */
	public static function executeMultiQuery($multiquery)
	{
		$queries = explode(';', $multiquery);
		$mysqli = DataBase::connect();
		mysqli_autocommit($mysqli, FALSE);
		mysqli_begin_transaction($mysqli);
		foreach($queries as $query) { 
			$result = mysqli_query($mysqli, $query);
			if(!$result) { 
				break;
			}
		}
			
		if(!$result) { 
			mysqli_rollback($mysqli);
			DataBase::close($mysqli);
			return false;
		}
		mysqli_commit($mysqli);
		DataBase::close($mysqli);
		return true;
	} 
}
?>
