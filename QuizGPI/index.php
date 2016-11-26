<?php

	require_once('php/controller.php');

	// Inicializamos los parametros de inicio
	$op = 'view';   // Opcion que vamos a elegir view o command
	$id = 0;	    
	$ac = 'front';	// Accion que vamos a realizar list(), add(), play(), ...

	if (isset($_GET["op"]))			$op["op"] = $_GET["op"];
	else if (isset($_POST["op"]))	$op["op"] = $_POST["op"];

	if (isset($_GET["id"]))			$id["id"] = $_GET["id"];
	else if (isset($_POST["id"]))	$id["id"] = $_POST["id"];	

	if (isset($_GET["ac"]))			$ac["ac"] = $_GET["ac"];
	else if (isset($_POST["ac"])) 	$ac["ac"] = $_POST["ac"];

	// Creamos un array con los 3 parametros	
	$var = array("op" => $op, "id" => $id, "ac" => $ac);	

	// Pasamos los parametros de usuario al controlador
	Controller::system($var);
?>