<?php

/* System not installed, yet!
----------------------------------*/
if( ! file_exists( dirname(__FILE__) . "/db-config.php" ) && basename( $_SERVER['REQUEST_URI'] ) !== 'install.php' ){
	header('Location: install.php');
	exit;
}

/* Save the root directory path in a constant
------------------------------------------------------*/
define( 'SITE_ROOT', dirname(__FILE__) . '/' );

/* Save the includes directory path in a constant
------------------------------------------------------*/
define( 'INC_DIR', SITE_ROOT . 'includes/' );

/* System is installed!
----------------------------*/
require_once dirname(__FILE__) . '/db-config.php';

/* Include core
--------------------*/
require_once INC_DIR . 'fields.php';
require_once INC_DIR . 'class-wp-hook.php';
require_once INC_DIR . 'plugin.php';
require_once INC_DIR . 'wp.php';

/* Include PHP Mailer library
----------------------------------*/
require_once SITE_ROOT . 'phpmailer/PHPMailerAutoload.php';

/* Adaptare PHPMailer pentru sistemul curent
-------------------------------------------------*/
require_once INC_DIR . 'mail-system.php';


/*  Conectare la baza de date
------------------------------------------------*/
function conectare_la_db(){
	$connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	return $connection;
}

/* Delete component by table name and ID
---------------------------------------------*/
function delete_admin_component( $section, $id ){
	if( !empty($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['id']) ){
		$conn     = conectare_la_db();
		$id       = mysqli_real_escape_string( $conn, intval($id) );
		$sql      = "DELETE FROM $section WHERE id = '$id'";
		
		if( mysqli_query($conn, $sql) ){
			return 'success';
		}
		else{
			return 'fail';
		}

		mysqli_close($conn);
	}
}

/* Functia de adaugare a unei pagini
------------------------------------------------*/
function adauga_pagina( $titlu, $continut = '', $meniu = '' ){
	$conn = conectare_la_db();
	$titlu = htmlspecialchars( mysqli_real_escape_string( $conn, $titlu ) );
	$continut = htmlspecialchars( mysqli_real_escape_string( $conn, $continut ) );

	// pregateste textul pentru url: https://css-tricks.com/snippets/php/create-url-slug-from-post-title/
	$url_text = strtolower( preg_replace('/[^A-Za-z0-9-]+/', '-', $titlu) );

	// Verifica daca o pagina cu titlul identic nu exista deja
	$verify_sql    = "SELECT * FROM pages WHERE url_text = '$url_text'";

	if( $result = mysqli_query($conn, $verify_sql) ){
		if( mysqli_num_rows( $result ) > 0 ){
			return '<div class="alert alert-warning">O pagina cu acest titlu exista deja. Introduceti alt titlu va rog.</div>';
		}
	}

	$continut = mysqli_real_escape_string( $conn, $continut );

	// Adauga pagina in baza de date
	$sql = "INSERT INTO pages VALUES (NULL, '$url_text', '$titlu', '$continut', CURRENT_TIMESTAMP, '$meniu')";
	if( mysqli_query($conn, $sql) ){
		return '<div class="alert alert-success">Pagina a fost adugata cu success!</div>';
	}
	else{
		return '<div class="alert alert-error">Eroare la adaugarea paginii. ' . mysqli_error($conn) .'</div>';
	}
	mysqli_close($conn);
}

/* Functia de editare a unei pagini
------------------------------------------------*/
function editeaza_pagina( $titlu, $continut = '', $meniu = '', $id ){
	$conn = conectare_la_db();
	$titlu = htmlspecialchars( mysqli_real_escape_string( $conn, $titlu ) );
	$continut = htmlspecialchars( mysqli_real_escape_string( $conn, $continut ) );

	// pregateste textul pentru url: https://css-tricks.com/snippets/php/create-url-slug-from-post-title/
	$url_text = strtolower( preg_replace('/[^A-Za-z0-9-]+/', '-', $titlu) );
	$id = mysqli_real_escape_string( $conn, $id );

	// Verifica daca o pagina cu titlul identic nu exista deja
	$verify_sql    = "SELECT * FROM pages WHERE url_text = '$url_text' AND id != $id";

	if( $result = mysqli_query($conn, $verify_sql) ){
		if( mysqli_num_rows( $result ) > 0 ){
			return '<div class="alert alert-error">O pagina cu acest titlu exista deja. Introduceti alt titlu va rog.</div>';
		}
	}

	// Adauga pagina in baza de date
	$sql = "UPDATE pages 
			SET url_text = '$url_text', titlu = '$titlu', continut = '$continut', meniu = '$meniu' 
			WHERE id = $id";

	if( mysqli_query($conn, $sql) ){
		return '<div class="alert alert-success">Pagina a fost editata cu success!</div>';
	}
	else{
		return '<div class="alert alert-error">Eroare la editarea paginii. ' . mysqli_error($conn) .'</div>';
	}
	mysqli_close($conn);
}

/* Primeste detaliile unei pagini.
------------------------------------------------*/
function primire_pagina( $page_name, $id = false ){
	if( empty( $page_name ) ){
		$page_name = $id;
		$column = 'id';
	}
	else{
		$column = 'url_text';
	}

	$conn     = conectare_la_db();
	$by       = mysqli_real_escape_string( $conn, strip_tags( $page_name ) );
	$sql      = "SELECT * FROM pages WHERE $column = '$by'";
	$result   = mysqli_query($conn, $sql);
	$row      = mysqli_fetch_array($result, MYSQLI_ASSOC);

	if( is_array( $row ) ){
		$row['continut'] = !empty( $row['continut'] ) ? htmlspecialchars_decode( $row['continut'] ) : '';
	}

	return $row;
	mysqli_close($conn);
}

function primire_pagina_dupa_id( $id ){
	return primire_pagina( false, $id );
}

/* genereaza toate paginile
--------------------------------*/
function toate_paginile(){
	$conn   = conectare_la_db();
	$sql    = "SELECT * FROM pages";
	$result = mysqli_query($conn, $sql);
	$rows   = mysqli_fetch_all($result, MYSQLI_ASSOC);

	if( is_array($rows) ){
		foreach ($rows as $id => $row) {
			$rows[$id]['continut'] = !empty( $row['continut'] ) ? htmlspecialchars_decode( $row['continut'] ) : '';
		}
	}

	return $rows;
	mysqli_close($conn);
}

/* Delete page
-------------------*/
function delete_page(){
	if( is_admin() && !empty($_GET['section']) && 'pages' == $_GET['section'] ){
		if( !empty($_GET['id']) ){
			$deleted = delete_admin_component( 'pages', intval($_GET['id']) );
			if( !empty($deleted) ){
				echo '<div class="alert alert-success">Pagina a fost stearsa cu succes.</div>';
			}
			else{
				echo '<div class="alert alert-danger">Eroare la stergerea unei paginii!</div>';
			}
		}
	}

}

/* Compara doua valori si afiseaza "selected" pentru optiunea selectata.
------------------------------------------------*/
function selected( $val, $opt ){
	if( $val == $opt ){
		echo ' selected="selected"';
	}
}

/* Titlul paginii curente
------------------------------------------------*/
function site_title( $show_home = false ){
	$site_title = '';

	if( !empty($_GET['page']) ){

		$page_name = strip_tags( $_GET['page'] );
		
		// Daca fisierul exista il includem
		if( file_exists( dirname(__FILE__) . "/{$page_name}.php" ) ){
			$site_title = ucfirst( str_replace('_', ' ', $page_name) );
		}
		
		// ... daca nu exista, cautam pagina in baza de date
		else{
			$pagina = primire_pagina( $page_name );
			if( !empty($pagina['titlu']) ){
				$site_title = $pagina['titlu'];
			}
		}
		
	}
	elseif( is_admin() ){
		$site_title = 'Admin';
	}
	elseif( $show_home ){
		$site_title = 'Acasa';
	}
	
	return $site_title;
}

/* Verifica daca pagina exista ca file or in BD
 - Returneaza `file` daca un fisier cu numele $page_name exista
 - Returneaza `dynamic` daca un fisier cu numele $page_name nu exista
   dar a fost gasita in baza de date.
 - Returneaza false daca conditiile precedente nu sunt adevarate.
----------------------------------------------------*/
function page_exists( $page_name ){
	$exists = false;
	
	$page_name = strip_tags( $page_name );
	
	// Daca fisierul exista il includem
	if( file_exists( dirname(__FILE__) . "/{$page_name}.php" ) ){
		$exists = 'file';
	}
	
	// ... daca nu exista, cautam pagina in baza de date
	else{
		$pagina = primire_pagina( $page_name );
		if( !empty($pagina['titlu']) ){
			$exists = 'dynamic';
		}
	}
	
	return $exists;
}

function get_page_url( $page_name ){
	return add_query_arg( array( 'page' => $page_name ), 'index.php' );
}
/* Verificarea daca e admin(backend) sau nu
---------------------------------------*/
function is_admin(){
	$request_uri = explode('?', $_SERVER['REQUEST_URI']);
	$base = basename($request_uri[0], '.php');
	return 'admin' == $base;
}

/* Verfiica daca suntem pe pagina principala
-------------------------------------------------*/
function is_home(){
	$request_uri = explode('?', $_SERVER['REQUEST_URI']);
	$base = basename($request_uri[0], '.php');
	return ( 'index' == $base && empty($_GET['page']) ) || ( !empty($_GET['page']) && ! page_exists($_GET['page']) );
}

/* Add user
----------------*/
function add_user( $username, $password, $email, $type = 'moderator' ){
	$message = array();

	if( !empty($username) && !empty($password) && !empty($email) && isset($type) ){

		$conn = conectare_la_db();

		// Verifica daca acest username exista deja
		$verify_sql = "SELECT * FROM users WHERE username = '$username'";

		if( $result = mysqli_query($conn, $verify_sql) ){
			if( mysqli_num_rows( $result ) > 0 ){
				$message['error'] = 'Acest username exista deja.';
			}
		}

		// Verifica daca acest email exista deja
		if( empty($message) ){
			$verify_sql = "SELECT * FROM users WHERE email = '$email'";

			if( $result = mysqli_query($conn, $verify_sql) ){
				if( mysqli_num_rows( $result ) > 0 ){
					$message['error'] = 'Acest email exista deja.';
				}
			}
		}

		// Introduce utilizatorul
		if( empty($message) ){
			$secure_pass = md5($password);
			$sql = "INSERT INTO users VALUES( NULL, '$username', '$secure_pass', '$email','$type' )";
			if( mysqli_query($conn, $sql) ){
				$message['success'] = 'Utilizatorul a fost inregistrat cu succes. Username: '. $username .' | Password: '. $password .'.';
			}
			else{
				$message['error'] = 'Eroare la inregistrarea utilizatorului.';
			}
		}
	}

	return $message;
}

/* Edit user
----------------*/
function edit_user( $id, $username, $password, $email, $type = 'moderator' ){
	$message = array();
	$id = intval($id);

	if( !empty($username) && isset($password) && !empty($email) && isset($type) ){

		$conn = conectare_la_db();

		// Verifica daca acest username exista deja
		$verify_sql = "SELECT * FROM users WHERE username = '$username' AND id <> '$id'";

		if( $result = mysqli_query($conn, $verify_sql) ){
			if( mysqli_num_rows( $result ) > 0 ){
				$message['error'] = 'Acest username exista deja.';
			}
		}

		// Verifica daca acest email exista deja
		if( empty($message) ){
			$verify_sql = "SELECT * FROM users WHERE email = '$email' AND id <> '$id'";

			if( $result = mysqli_query($conn, $verify_sql) ){
				if( mysqli_num_rows( $result ) > 0 ){
					$message['error'] = 'Acest email exista deja.';
				}
			}
		}

		// Introduce utilizatorul
		if( empty($message) ){
			$secure_pass = md5($password);
			$sql = "UPDATE
					  users
					SET
					  username = '$username',
					  password = '$secure_pass',
					  email = '$email',
					  type = '$type'
					WHERE
					  id = $id";

			if( mysqli_query($conn, $sql) ){
				$message['success'] = 'Utilizatorul a fost editat cu succes.';
			}
			else{
				$message['error'] = 'Eroare la editarea utilizatorului.';
			}
		}
	}

	return $message;
}

/* Primeste datele pentru un utilizator din db.
----------------------------------------------------*/
function get_users(){
	$conn   = conectare_la_db();
	$sql    = "SELECT * FROM users";
	$result = mysqli_query($conn, $sql);
	$users   = mysqli_fetch_all($result, MYSQLI_ASSOC);

	if( is_array($users) ){
		foreach ($users as $id => $user) {
			$users[$id]['id'] = !empty( $user['id'] ) ? $user['id'] : '';
			$users[$id]['username'] = !empty( $user['username'] ) ? $user['username'] : '';
			$users[$id]['email'] = !empty( $user['email'] ) ? $user['email'] : '';
			$users[$id]['type'] = !empty( $user['type'] ) ? $user['type'] : '';
		}
	}

	return $users;
	mysqli_close($conn);
}

function get_user_by_id( $id = false ){
	$conn     = conectare_la_db();
	$by       = mysqli_real_escape_string( $conn, strip_tags( $id ) );
	$sql      = "SELECT * FROM users WHERE id = '$by'";
	$result   = mysqli_query($conn, $sql);
	$row      = mysqli_fetch_array($result, MYSQLI_ASSOC);

	return $row;
	mysqli_close($conn);
}

function get_user_by_username( $username = false ){
	$conn     = conectare_la_db();
	$by       = mysqli_real_escape_string( $conn, strip_tags( $username ) );
	$sql      = "SELECT * FROM users WHERE username = '$by'";
	$result   = mysqli_query($conn, $sql);
	$row      = mysqli_fetch_array($result, MYSQLI_ASSOC);

	return $row;
	mysqli_close($conn);
}

function user_is( $username, $type ){
	$user = get_user_by_username( $username );
	if( !empty($user['type']) ){

		//Superadmin can do anything
		if( $user['type'] == 'superadmin'){
			return true;
		}

		// Else if is admin
		elseif( $user['type'] == 'admin' && 'moderator' == $type ){
			return true;
		}

		else{
			return $user['type'] == $type;
		}
	}
	else{
		return false;
	}
}

function current_user_is( $type ){
	return user_is( $_SESSION['username'], $type );
}

/* Delete user
-------------------*/
function delete_user(){
	if( is_admin() && !empty($_GET['section']) && 'users' == $_GET['section'] ){
		if( !empty($_GET['id']) ){

			// Do not allow to delete superadmins
			if( user_is( $_GET['id'], 'superadmin' ) )
				return false;

			// Do not allow to delete other user with the same type
			if( user_is( $_GET['id'], 'admin' ) && current_user_is( 'admin' ) )
				return false;

			// Only admins can delete users
			if( ! current_user_is( 'admin' ) )
				return false;

			$deleted = delete_admin_component( 'users', intval($_GET['id']) );
			if( !empty($deleted) ){
				echo '<div class="alert alert-success">Utilizatorul a fost eliminat.</div>';
			}
			else{
				echo '<div class="alert alert-danger">Eroare la eliminarea utilizatorului!</div>';
			}
		}
	}
}

/* Delete cerere
---------------------*/
function delete_cerere(){
	if( is_admin() && !empty($_GET['section']) && 'cereri' == $_GET['section'] ){
		if( !empty($_GET['id']) ){

			if( ! current_user_is( 'moderator' ) )
				return false;

			$deleted = delete_admin_component( 'cereri', intval($_GET['id']) );
			if( !empty($deleted) ){
				echo '<div class="alert alert-success">Cererea a fost eliminata.</div>';
			}
			else{
				echo '<div class="alert alert-danger">Eroare la eliminarea cererrii!</div>';
			}
		}
	}
}

/* Update cerere
---------------------*/
function update_cerere_status( $id, $status ){
	$conn     = conectare_la_db();
	$option   = intval( $id );

	$sql = "UPDATE cereri SET status = '$status' WHERE id = '$id'";
	
	if( mysqli_query($conn, $sql) ){
		return true;
	}
	else{
		return false;
	}

	mysqli_close($conn);
}


/* Get cerereby id
-----------------------*/
function get_cerere_by_id( $id = false ){
	$conn     = conectare_la_db();
	$by       = mysqli_real_escape_string( $conn, strip_tags( $id ) );
	$sql      = "SELECT * FROM cereri WHERE id = '$by'";
	$result   = mysqli_query($conn, $sql);
	$row      = mysqli_fetch_array($result, MYSQLI_ASSOC);

	if( !empty($row) ){
		$datele = maybe_unserialize( htmlspecialchars_decode( $row['datele'] ) );
		unset($row['datele']);
		$row = array_merge( $row, $datele );
	}

	return $row;

	mysqli_close($conn);
}

/* Update or add option
----------------------------*/
function update_option( $option, $value ){
	$conn     = conectare_la_db();
	$option   = strip_tags( maybe_serialize( $option ) );
	$value    = htmlspecialchars( mysqli_real_escape_string( $conn, maybe_serialize( $value ) ) );

	$old_value = get_option( $option );

	if( isset($old_value) ){
		$sql = "UPDATE options SET value = '$value' WHERE option = '$option'";
	}
	else{
		$sql = "INSERT INTO options VALUES( NULL, '$option', '$value' )";
	}
	
	if( mysqli_query($conn, $sql) ){
		return true;
	}
	else{
		return false;
	}

	mysqli_close($conn);
}

/* Update or add option
----------------------------*/
function get_option( $option, $default_value = null ){
	$conn     = conectare_la_db();
	$option   = strip_tags( maybe_serialize( $option ) );
	$sql      = "SELECT * FROM options WHERE option = '$option' LIMIT 1";
	
	if( $result = mysqli_query($conn, $sql) ){
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if( !isset( $row['value'] ) ){
			return $default_value;
		}
		else{
			return maybe_unserialize( htmlspecialchars_decode( $row['value'] ) );
		}
	}
	else{
		return $default_value;
	}

	mysqli_close($conn);
}

/* Delete an option
----------------------------*/
function delete_option( $option ){
	$conn     = conectare_la_db();
	$option   = strip_tags( maybe_serialize( $option ) );
	$sql      = "DELETE FROM options WHERE option = '$option'";
	
	if( $result = mysqli_query($conn, $sql) ){
		return true;
	}
	else{
		return false;
	}

	mysqli_close($conn);
}

/* Source: https://stackoverflow.com/a/43699922/1050262
------------------------------------------------------------*/
/**
 * Get the base URL of the current page. For example, if the current page URL is
 * "https://example.com/dir/example.php?whatever" this function will return
 * "https://example.com/dir/" .
 *
 * @return string The base URL of the current page.
 */
function get_site_url() {

	$protocol = filter_input(INPUT_SERVER, 'HTTPS');
	if (empty($protocol)) {
		$protocol = "http";
	}

	$host = filter_input(INPUT_SERVER, 'HTTP_HOST');

	$request_uri_full = filter_input(INPUT_SERVER, 'REQUEST_URI');
	$last_slash_pos = strrpos($request_uri_full, "/");
	if ($last_slash_pos === FALSE) {
		$request_uri_sub = $request_uri_full;
	}
	else {
		$request_uri_sub = substr($request_uri_full, 0, $last_slash_pos + 1);
	}

	return $protocol . "://" . $host . $request_uri_sub;

}


/*
-------------------------------------------------------------------------------
Forms
-------------------------------------------------------------------------------
*/

function form_fields(){
	return array(

		'Datele asiguratului',

		'statut_juridic' => array( 
			'label' => 'Statutul juridic al asiguratului',
			'type' => 'nice_selector',
			'options' => array(
				'fizica' => 'Persoana Fizica',
				'juridica' => 'Persoana Juridica',
			),
		),
		'resedinta_sofer' => array( 
			'label' => 'Reședința șoferului/asiguratului',
			'type' => 'nice_selector',
			'options' => array(
				'mun_chisinau' => 'Mun. Chisinau',
				'mun_balti' => 'Mun. Balti',
				'alte_localitati' => 'Alte Localitati',
			),
		),
		'virsta_conducator' => array( 
			'label' => 'Vîrsta conducătorului auto (inclusiv persoanele admise la volan)', 
			'type' => 'nice_selector',
			'options' => array(
				'23-' => 'pina la 23 ani',
				'23+' => 'peste 23 ani',
			),
			'show_if' => 'statut_juridic::fizica',
		),
		'stagiu_conducator' => array( 
			'label' => 'Stagiul conducătorului auto (inclusiv persoanele admise la volan)',
			'type' => 'select',
			'options' => array(
		 		'1' => 'pina la 1 an',
				'2' => 'pina la 2 ani',
				'3' => 'pina la 3 ani',
				'4' => 'pina la 4 ani',
				'5' => 'pina la 5 ani',
				'6' => 'pina la 6 ani',
				'7' => 'pina la 7 ani',
				'8' => 'pina la 8 ani',
				'8+' => 'peste 8 ani', 	
			),
			'show_if' => 'statut_juridic::fizica',
		),
		'pensionar' => array( 
			'label' => 'Sunteţi pensionar sau aveţi grad de invaliditate?',
			'type' => 'nice_selector',
			'options' => array(
				'nu' => 'Nu',
				'da' => 'Da',
			),
			'show_if' => 'statut_juridic::fizica',
		),
		'rca_contract_vechi' => array( 
			'label' => 'Ați avut încheiat un contract de asigurare RCA în ultimii 2 ani?', 
			'type' => 'nice_selector',
			'options' => array(
				'nu' => 'Nu',
				'da' => 'Da',
			),
			'show_if' => 'statut_juridic::fizica',
		),
		'asigurat_precedent_juridic' => array( 
			'label' => 'Autovehiculul dat a fost asigurat şi anul precedent de această persoană juridică?', 
			'type' => 'nice_selector',
			'options' => array(
				'nu' => 'Nu',
				'da' => 'Da',
			),
			'show_if' => 'statut_juridic::juridica',
		),
		'accidente' => array( 
			'label' => 'În cîte accidente ați fost implicat în ultimii 2 ani?',
			'type' => 'select',
			'options' => array(
				'0' => '0',
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4+' => 'peste 4',
			),
			'show_if' => array( 
				'rca_contract_vechi::da',
				'asigurat_precedent_juridic::da',
			),
		),
		'utilizare_vehicul' => array( 
			'label' => 'Modul de utilizare a autovehiculului',
			'type' => 'select',
			'options' => array(
				'personal' => 'Uz personal',
				'temporar' => 'Autovehicul inmatriculat cu numere temporare',
				'agricol' => 'Autovehicul utilizat in activitati agricole sezoniera',
				'altele' => 'Autovehicul aflat in alt caz prevazut de legislatie',
			),
		),

		'Datele autovehiculului',

		'tip_vehicul' => array( 
			'label' => 'Tipul vehiculului',
			'type' => 'select',
			'options' => array(
				'autoturism' => 'Autoturism (destinat transportului de persoane cu pina la 9 locuri, inclusiv conducatorul)',
				'autobuz' => 'Vehicul destinat transportului de persoane (Autobuz)',
				'tractor' => 'Tractor rutier (altele decit tractoarele pentru semiremorci) cu o capacitate a motorului',
				'autocamion' => 'Autocamion cu masa maxima autorizata',	
				'motocicleta' => 'Motocicleta',
			),
		),
		'capacitate_cilindrica_autoturism' => array( 
			'label' => 'Capacitatea cilindrică',
			'type' => 'select',
			'options' => array(
				'1200-' => 'pina la 1200 cm.3',
				'1201+' => 'de la 1201 pina la 1600 cm.3',
				'1601+' => 'de la 1601 pina la 2000 cm.3',
				'2001+' => 'de la 2001 pina la 2400 cm.3',
				'2401+' => 'de la 2401 pina la 3000 cm.3',
				'3000+' => 'peste 3000 cm.3',
			),
			'show_if' => 'tip_vehicul::autoturism',
		),
		'numarul_de_locuri_autobuz' => array( 
			'label' => 'Numărul de locuri',
			'type' => 'select',
			'options' => array(
				'vehicul10+' => 'Vehicul destinat transportului de persoane cu 10-17 locuri, inclusiv conducatorul',
				'vehicul18+' => 'Vehicul destinat transportului de persoane cu 18-30 locuri, inclusiv conducatorul',
				'vehicul30+' => 'Vehicul destinat transportului de persoane cu peste 30 locuri, inclusiv conducatorul',
				'troilebuz' => 'Troilebuz',
			),
			'show_if' => 'tip_vehicul::autobuz',
		),
		'putere_motor_tractor_rutier' => array( 
			'label' => 'Puterea motorului',
			'type' => 'select',
			'options' => array(
				'45-' => 'Pina la 45 c.p.,inclusiv',
				'46+' => 'De la 46 c.p., pina la 100 c.p., inclusiv',
				'100+' => 'Peste 100 c.p.',
			),
			'show_if' => 'tip_vehicul::tractor',
		),
		'masa_autorizata_autocamion' => array( 
			'label' => 'Masa maximă autorizată', 
			'type' => 'select',
			'options' => array(
				'3500-' => 'Pina la 3500 kg.',
				'3501+' => 'De la 3501 pina la 7500 kg.',
				'7501+' => 'De la 7501 pina la 16000 kg.',
				'16000+' => 'Peste 16000 kg.',
			),
			'show_if' => 'tip_vehicul::autocamion',
		),
		'capacitate_cilindrica_motocicleta' => array( 
			'label' => 'Capacitatea cilindrică',
			'type' => 'select',
			'options' => array(
				'300-' => 'Pina la 300 cm.3',
				'300+' => 'Peste 300 cm.3',
			),
			'show_if' => 'tip_vehicul::motocicleta',
		),
		'inmatriculat_tara' => array( 
			'label' => 'Vehicul înmatriculat în', 
			'type' => 'nice_selector',
			'options' => array(
				'moldova' => 'Republica Moldova',
				'strainatate' => 'Strainatate',
			),
		),
		'persoane_admise_volan' => array( 
			'label' => 'Numărul persoanelor admise la volan', 
			'type' => 'select',
			'options' => array(
				'limitat' => 'Limitat (cu indicare a pina la 3 persoane)',
				'nelimitat' => 'Nelimitat',
			),
		),
		'carte_verde_europa' => array( 
			'label' => 'Dețineți Carte Verde pentru Europa (12 luni)?', 
			'type' => 'nice_selector',
			'options' => array(
				'nu' => 'Nu',
				'da' => 'Da',
			),
		),

		'Date polita',

		'perioada_asigurata' => array( 
			'label' => 'Perioada asigurată', 
			'type' => 'select',
			'options' => array(
				'15z' => '15 zile',
				'1l' => '1 luna',
				'2l' => '2 luni',
				'3l' => '3 luni',
				'4l' => '4 luni',
				'5l' => '5 luni',
				'6l' => '6 luni',
				'7l' => '7 luni',
				'8l' => '8 luni',
				'9l' => '9 luni',
				'10l' => '10 luni',
				'11l' => '11 luni',
				'12l' => '12 luni',
			),
			'grid' => 'col-sm-4',
		),
		'compania_asigurare' => array( 
			'label' => 'Compania de Asigurare', 
			'type' => 'select',
			'options' => array(
				'moldasig' => 'Moldasig',
				'donaris_group' => 'Donaris Group',
				'garantie' => 'Garantie',
				'moldcargo' => 'Moldcargo',
				'transelit' => 'Transelit',
				'alliance_insurance_group' => 'Alliance Insurance Group',
				'grawe_carat' => 'Grawe Carat',
				'klassika' => 'Klassika',
			),
			'grid' => 'col-sm-8',
			'clear_row' => true,
		),

		'Formularul de comanda',

		'tip_document' => array( 
			'label' => 'Tip document',
			'type' => 'nice_selector',
			'size' => 'large',
			'options' => array(
				'vechi' => array(
					'label' => 'Certificat de înmatriculare tip vechi',
					'img' => 'img/vechi.jpg',
				),
				'nou' => array(
					'label' => 'Certificat de înmatriculare tip nou',
					'img' => 'img/nou.jpg',
				),
				'temporar' => array(
					'label' => 'Certificat de înmatriculare temporar',
					'img' => 'img/temporar.jpg',
				),
			),
		),

		'email' => array( 
			'label' => 'Email',
			'type' => 'text',
			'grid' => 'col-sm-6',
		),

		'telefon' => array( 
			'label' => 'Telefon',
			'type' => 'text',
			'grid' => 'col-sm-6',
		),

		'nume_prenume' => array( 
			'label' => 'Numele și Prenume',
			'type' => 'text',
			'grid' => 'col-sm-6',
			'img_tip' => true,
		),
		'cod_personal' => array( 
			'label' => 'Codul personal',
			'type' => 'text',
			'grid' => 'col-sm-6',
			'clear_row' => true,
			'img_tip' => true,
		),
		'drept_posesiune_vehicul' => array( 
			'label' => 'Drept de posesiune a autovehiculului', 
			'type' => 'select',
			'options' => array(
				'personal' => 'Personal',
				'leasing' => 'Leasing',
				'locatiune' => 'Locatiune (comodat)',
				'procura' => 'Procura si alte titluri',
			),
			'grid' => 'col-sm-6',
		),
		'numar_inmatriculare_document' => array( 
			'label' => 'Numărul de înmatriculare al documentului',
			'type' => 'text',
			'grid' => 'col-sm-6',
			'clear_row' => true,
			'img_tip' => true,
		),
		'numar_inregistrare_vehicul' => array( 
			'label' => 'Numărul de înregistrare al autovehiculului', 
			'type' => 'text',
			'grid' => 'col-sm-6',
			'img_tip' => true,
		),
		'an_fabricatie' => array( 
			'label' => 'An fabricație',
			'type' => 'select',
			'default' => 2010,
			'options' => function() {
				$years = array();
				for ($i = 1900; $i <= date( 'Y' ); $i++) { 
					$years[ $i ] = $i;
				}
				return $years;
			},
			'grid' => 'col-sm-6',
			'clear_row' => true,
		),
		'marca' => array( 
			'label' => 'Marca/Model', 
			'type' => 'text',
			'grid' => 'col-sm-3',
		),
		'tip_autovehicul' => array( 
			'label' => 'Tipul autovehiculului', 
			'type' => 'text',
			'grid' => 'col-sm-3',
		),
		'capacitate_cilindrica' => array( 
			'label' => 'Capacitatea cilindrică', 
			'type' => 'number',
			'grid' => 'col-sm-3',
		),
		'masa_proprie' => array( 
			'label' => 'Masa proprie', 
			'type' => 'number',
			'grid' => 'col-sm-3',
			'clear_row' => true,
		),
		'masa_max_autorizata' => array( 
			'label' => 'Masa maximă autorizată',
			'type' => 'number',
			'grid' => 'col-sm-3',
		),
		'numar_locuri' => array( 
			'label' => 'Numărul de locuri', 
			'type' => 'number',
			'min' => 2,
			'max' => 20,
			'grid' => 'col-sm-3',
		),
		'numar_caroserie' => array( 
			'label' => 'Numărul caroseriei', 
			'type' => 'text',
			'grid' => 'col-sm-3',
		),
		'numar_motor' => array( 
			'label' => 'Numărul motorului',	 
			'type' => 'text',
			'grid' => 'col-sm-3',
			'clear_row' => true,
		),	
		'persoane_admin_la_volan' => array( 
			'label' => 'Persoane admise la volan',	 
			'type' => 'persoane_admin_la_volan',
			'clear_row' => true,
		),	
	);
}

function form_label( $id ){
	$fields = form_fields();

	if( isset($fields[ $id ]) && !empty($fields[ $id ]['label']) ){
		return $fields[ $id ]['label'];
	}
	else{
		return $id;
	}
}

function form_value_label( $field_id, $saved_value ){
	$label = $saved_value;

	$fields = form_fields();

	$type = $fields[ $field_id ]['type'];
	$options = !empty($fields[ $field_id ]['options']) ? $fields[ $field_id ]['options'] : false;
	
	if( in_array( $type, array( 'select', 'radio', 'nice_selector' ) ) && is_array( $options ) ){
		$label = $options[ $saved_value ];
		if( is_array( $label ) ){
			$label = $label['label'];
		}
	}

	return $label;
}

function process_form_date_asigurat(){
	if( !empty($_POST['form_date_asigurat']) ) :
		
		$data = $_POST;


		/* Escape data
		-------------------*/
		$data['email'] = htmlspecialchars($data['email']);
		$data['telefon'] = htmlspecialchars($data['telefon']);
		$data['nume_prenume'] = htmlspecialchars($data['nume_prenume']);
		$data['cod_personal'] = htmlspecialchars($data['cod_personal']);
		$data['numar_inmatriculare_document'] = htmlspecialchars($data['numar_inmatriculare_document']);
		$data['numar_inregistrare_vehicul'] = htmlspecialchars($data['numar_inregistrare_vehicul']);


		/* Verificare date
		-----------------------*/
		$errors = array();

		$fields = array( 
			'email',
			'telefon',
			'nume_prenume',
			'cod_personal',
			'numar_inmatriculare_document',
			'numar_inregistrare_vehicul', 
			'marca',
			'tip_autovehicul',
			'capacitate_cilindrica',
			'masa_proprie',
			'masa_max_autorizata',
			'numar_locuri',
			'numar_caroserie',
			'numar_motor',
			'persoane_admin_la_volan',
		);

		foreach ($fields as $field) {
			if( empty( $data[ $field ] ) )
				$errors[ $field ] = 'Introduceti "' . form_label( $field ) . '".';
		}


		if( !empty($errors) )
			return $errors;

		
		/* Conectare la BD
		------------------------------*/
		$conn = conectare_la_db();

		if( ! $conn ){
			$errors = 'Conexiune imposibila la baza de date!';
		}

		if( !empty($errors) )
			return $errors;


		/* Pregatim coloanele principale
		-------------------------------------*/
		$email        = htmlspecialchars($data['email']);
		$telefon      = htmlspecialchars($data['telefon']);
		$nume         = htmlspecialchars($data['nume_prenume']);
		$cod_personal = htmlspecialchars($data['cod_personal']);
		$nr_doc       = htmlspecialchars($data['numar_inmatriculare_document']);
		$nr_vehicul   = htmlspecialchars($data['numar_inregistrare_vehicul']);


		/* Serialize data and escape
		---------------------------------*/
		$datele = htmlspecialchars( serialize( $data ) );


		/* Introduce datele in BD
		------------------------------*/
		$sql = "INSERT INTO cereri (
			email,
			telefon,
			nume_prenume,
			cod_personal, 
			numar_inmatriculare_document, 
			numar_inregistrare_vehicul, 
			status, 
			datele
		)
		VALUES (
			'$email',
			'$telefon',
			'$nume',
			'$cod_personal',
			'$nr_doc',
			'$nr_vehicul',
			'pending',
			'$datele'
		)";

		$retval = mysqli_query( $conn, $sql );

		if( ! $retval ) {
			$errors[] = 'Nu putem introduce datele in BD';
		}
		else{
			do_action( 'cerere_inregistrata_cu_success', $data );
		}

		mysqli_close($conn);


		/* Return response
		-----------------------*/
		if( !empty($errors) ){
			return $errors;
		}
		else{
			return true; 
		}

	endif;
}

/* Include headerul(partea de sus)
------------------------------------------------*/
function get_header(){
	include "header.php";
}

/* Include footerul(partea de jos)
------------------------------------------------*/
function get_footer(){
	include "footer.php";
}


function price_table(){
	return array(
		'K1' => array(
			'autoturism' => array(
				'1200-' => 0.7,
				'1201+' => 1,
				'1601+' => 1.1,
				'2001+' => 1.2,
				'2401+' => 1.5,
				'3000+' => 3,
			),
			'autobuz' => array(
				'vehicul10+' => 1.5,
				'vehicul18+' => 2,
				'vehicul30+' => 2.2,
				'troilebuz' => 3,
			),
			'tractor' => array(
				'45-' => 0.5,
				'46+' => 0.7,
				'100+' => 0.9,
			),
			'autocamion' => array(
				'3500-' => 1.5,
				'3501+' => 1.7,
				'7501+' => 2,
				'16000+' => 2.5,
			),	
			'motocicleta' => array(
				'300-' => 0.3,
				'300+' => 0.5,
			),
		),
		'K2' => array(
			'mun_chisinau' => 1.4,
			'mun_balti' => 1,
			'alte_localitati' => 0.9
		),
		'K3' => array(
			'fizica' => 0.9,
			'juridica' => 1.5,
		),
		'K4' => 1.2,
		'K5' => array(
			'23-' => array(
				'2-' => 1.2,
				'2+' => 1.1,
			),
			'23+' => array(
				'2-' => 1,
				'2+' => 0.9,
			),
		),
	);
}

function calculator_rca(){
	if( empty($_POST) )
		return false;

	$data = $_POST;
	$k1 = $k2 = $k3 = $k4 = $k5 = 1;
	$price = price_table();

	// K1
	switch ( $data['tip_vehicul'] ) {
		case 'autoturism':
				$k1 = $price['K1']['autoturism'][ $data[ 'capacitate_cilindrica_autoturism' ] ];
			break;
		case 'autobuz':
				$k1 = $price['K1']['autobuz'][ $data[ 'numarul_de_locuri_autobuz' ] ];
			break;
		case 'tractor':
				$k1 = $price['K1']['tractor'][ $data[ 'putere_motor_tractor_rutier' ] ];
			break;
		case 'autocamion':
				$k1 = $price['K1']['autocamion'][ $data[ 'masa_autorizata_autocamion' ] ];
			break;
		case 'motocicleta':
				$k1 = $price['K1']['motocicleta'][ $data[ 'capacitate_cilindrica_motocicleta' ] ];
			break;
	}

	// K2
	$k2 = $price['K2'][ $data['resedinta_sofer'] ];

	// K3
	$k3 = $price['K3'][ $data['statut_juridic'] ];

	// K4
	if( 'nelimitat' == $data['persoane_admise_volan'] || 'juridica' == $data['statut_juridic'] ){
		$k4 = $price['K4'];
	}

	//K5
	else{
		$stagiu = intval( $data['stagiu_conducator'] );
		if( $stagiu < 3 ){
			$k5 = $price['K5'][ $data['virsta_conducator'] ]['2-'];
		}
		else{
			$k5 = $price['K5'][ $data['virsta_conducator'] ]['2+'];
		}
	}


	echo round( 715 * $k1 * $k2 * $k3 * $k4 * $k5 );
}