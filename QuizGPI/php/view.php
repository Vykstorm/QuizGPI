<?php

/**
* 	Clase para gestionar las vistas
*/
class View
{
	
	/* Función para cargar un fichero html determinado*/
	protected static function getHtml($name)
	{
		switch ($name) {
			case 'front':
				$path = "html/front.html";
				break;
			case 'menu':
				
				break;

			default:
				# code...
				break;
		}

		$text = file_get_contents($path) or exit("Error getHtml(".$name.")\npath=".$path);
		return $text;
	}

	/* Carga la página de inicio */
	public static function front()
	{
		// Tener en cuenta sesion de usuario
    	// Cargar datos dinámicamente
		$text = View::getHtml("front");
		echo $text;
	}

	


}
?>