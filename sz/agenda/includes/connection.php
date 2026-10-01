<?php
$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
		
	//define('DB_HOST', 'localhost'); 
	/*
	define('DB_USERNAME', 'root'); 
	define('DB_PASSWORD', ''); 
	define('DATABASE', 'sz'); 
	define('TABLE', 'calendar');
	define('USERS_TABLE', 'users');
	*/
	
	
	if (!defined('DB_HOST')) {
		define('DB_HOST', 'localhost');
	}
	if (!defined('DB_USERNAME')) {
		define('DB_USERNAME', 'c0780240_sz');
	}
	if (!defined('DB_PASSWORD')) {
		define('DB_PASSWORD', '48kobuniFA');
	}
	if (!defined('DATABASE')) {
		define('DATABASE', 'c0780240_sz');
	}
	if (!defined('TABLE')) {
		define('TABLE', 'calendar');
	}
	if (!defined('USERS_TABLE')) {
		define('USERS_TABLE', 'users');
	}
	
	/* Peticiones AJAX (cal_events.php, etc.) no pasan por conexion.php del sitio: abrir $con aquí */
	if (!isset($con) || !$con) {
		$con = @mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DATABASE);
	}
	
	$categories[] ="";
	$categories[] ="General";
	
	if ($con && $user_id > 0) {
		$query = mysqli_query($con,"SELECT * from agenda_temas where id_usuario=$user_id");
		if ($query) {
			while($row = mysqli_fetch_array($query)){
				$categories[] =$row['nombre'];
			}
		}
	}
	
	if (!defined('SITE_FILES_URL')) {
		define('SITE_FILES_URL', '');
	}
	
	// Default Categories
	
	//$categories = array("General","Viaje","Reunion","Visita Nacional", "Otro");
	
	/*
	Only applied for non user versions
	Should (non admin versions) display user events from the database?
	 true - does not display user events 
	 false - will display all events on the database even private ones on non admin versions (e.g: 'Simple')
	*/
	if (!defined('PUBLIC_PRIVATE_EVENTS')) {
		define('PUBLIC_PRIVATE_EVENTS', true);
	}
	
	// Feature to import events
	if (!defined('IMPORT_EVENTS')) {
		define('IMPORT_EVENTS', true);
	}
	
?>