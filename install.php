<?php 

if( file_exists( dirname(__FILE__) . "/db-config.php" ) ) {
	header('Location: index.php');
	exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Page</title>
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap-theme.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script type="text/javascript" src="js/jquery-2.2.4.min.js"></script>

</head>
<body>
<div class="page-install">

<?php

$db_host        = 'localhost';
$db_username    = '';
$db_password    = '';
$db_name        = '';
$admin_username = '';
$admin_password = '';
$admin_email    = '';
$site_title    = '';
$errors         = array();


if( !empty($_POST) ){ 

	/* Verificam datele introduse
	------------------------------------------------*/
	if( !empty($_POST['db-host']) ){
		$db_host = strip_tags($_POST['db-host']);
	}
	else{
		$errors['db-host'] = '<div class="alert alert-danger">Introduceti DB Host.</div>';
	}

	if( !empty($_POST['db-username']) ){
		$db_username = strip_tags($_POST['db-username']);
	}
	else{
		$errors['db-username'] = '<div class="alert alert-danger">Introduceti DB username.</div>';
	}

	// DB poate fi fara parola, de aceea nu vom afisa nici o eroare
	$db_password = strip_tags($_POST['db-password']);

	if( !empty($_POST['db-name']) ){
		$db_name = strip_tags($_POST['db-name']);
	}
	else{
		$errors['db-name'] = '<div class="alert alert-danger">Introduceti DB name.</div>';
	}

	if( !empty($_POST['admin-username']) ){
		$admin_username = strip_tags($_POST['admin-username']);
	}
	else{
		$errors['admin-username'] = '<div class="alert alert-danger">Introduceti Administrator username.</div>';
	}

	if( !empty($_POST['admin-password']) ){
		$admin_password = strip_tags($_POST['admin-password']);
	}
	else{
		$errors['admin-password'] = '<div class="alert alert-danger">Introduceti Administrator password.</div>';
	}

	if( !empty($_POST['admin-email']) ){
		$admin_email = strip_tags($_POST['admin-email']);
	}
	else{
		$errors['admin-email'] = '<div class="alert alert-danger">Introduceti Administrator email.</div>';
	}

	if( !empty($_POST['site-title']) ){
		$site_title = strip_tags($_POST['site-title']);
	}
	else{
		$errors['site-title'] = '<div class="alert alert-danger">Introduceti titlul site-ului.</div>';
	}

	if( !empty($db_host) && !empty($db_username) && !empty($db_name) && !empty( $admin_username ) && !empty( $admin_password ) && !empty( $admin_email ) && !empty( $site_title )  ){

		/* Conectare la BD
		------------------------------------------------*/
		$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

		// Verifica conexiunea
		if (!$conn) {
			$errors['conexiune-imposibila'] = '<div class="alert alert-danger">Conexiune imposibila!</div>';
		}
		else{
			$success_msg = array();
			$error_msg = array();

			/* Creaza tabela pentru pagini
			------------------------------------------------*/
			$sql = "CREATE TABLE pages (
			id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
			url_text VARCHAR(255),
			titlu VARCHAR(255),
			continut LONGTEXT,
			data_adaugarii TIMESTAMP,
			meniu VARCHAR(30)
			)";
			if( mysqli_query($conn, $sql) ){
				$success_msg[] = 'Tabelul "pages" a fost creat.';
			}
			else{
				$error_msg[] = 'Eroare la crearea tabelului "pages".';
			}

			/* Creaza tabela pentru utilizatori
			------------------------------------------------*/
			$sql = "CREATE TABLE users (
			id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
			username VARCHAR(255),
			password VARCHAR(255),
			email VARCHAR(255),
			type VARCHAR(255)
			)";
			if( mysqli_query($conn, $sql) ){
				$success_msg[] = 'Tabelul "users" a fost creat.';
			}
			else{
				$error_msg[] = 'Eroare la crearea tabelului "users".';
			}

			/* Creaza tabela pentru cereri
			------------------------------------------------*/
			$sql = "CREATE TABLE cereri(
			id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			email VARCHAR(255),
			telefon VARCHAR(255),
			nume_prenume VARCHAR(255),
			cod_personal int,
			numar_inmatriculare_document VARCHAR(255),
			numar_inregistrare_vehicul VARCHAR(255),
			status VARCHAR(255),
			datele LONGTEXT
			)";
			if( mysqli_query($conn, $sql) ){
				$success_msg[] = 'Tabelul "cereri" a fost creat.';
			}
			else{
				$error_msg[] = 'Eroare la crearea tabelului "cereri".';
			}

			/* Creaza tabela pentru optiuni
			------------------------------------------------*/
			$sql = "CREATE TABLE options(
			id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			option VARCHAR(255),
			value LONGTEXT
			)";
			if( mysqli_query($conn, $sql) ){
				$success_msg[] = 'Tabelul "options" a fost creat.';
			}
			else{
				$error_msg[] = 'Eroare la crearea tabelului "options".';
			}

	
			/* Introducem datele administratorului
			------------------------------------------------*/
			$secure_pass = md5($admin_password);
			$sql = "INSERT INTO users VALUES( NULL, '$admin_username', '$secure_pass', '$admin_email', 'superadmin' )";
			if( mysqli_query($conn, $sql) ){
				$success_msg[] = "Administratorul a fost inregistrat cu succes.<br> Username: $admin_username | Password: $admin_password.";
			}
			else{
				$error_msg[] = 'Eroare inregistrarea administratorului.';
			}

			/* Inchidem conexiunea
			------------------------------------------------*/
			mysqli_close($conn);

/* Creaza fisierul db-config.php
------------------------------------------------*/
$content = "<?php
define('DB_HOST',     '" . $db_host ."');
define('DB_USERNAME', '" . $db_username ."');
define('DB_PASSWORD', '" . $db_password ."');
define('DB_NAME',     '" . $db_name ."');";

			// Creaza fisierul si adauga continutul
			file_put_contents('db-config.php', $content);

			echo '
			<div class="install-success">
				<img src="img/success.png" alt="" />
				<h1>Instalare reușită</h1><ul class="install-log-list">';
				
				if( !empty($success_msg) ){
					foreach ($success_msg as $msg) {
						echo '<li class="bg-success">'. $msg .'</li>';
					}
				}

				if( !empty($error_msg) ){
					foreach ($error_msg as $msg) {
						echo '<li class="bg-danger">'. $msg .'</li>';
					}
				}

			echo '</ul>
			<a href="index.php" class="btn btn-primary">Continuare</a>
			</div>
			';

			// Instalarea a fost efectuata cu success. Acum putem include functions.php si
			// de asemenea putem introduce datele implicite in DB.
			require_once dirname(__FILE__) . "/functions.php";

			update_option( 'site_title', $_POST['site-title'] );
			update_option( 'site_email', $_POST['admin-email'] );

		}
	}

}

// Daca forma nu a fost trimisa sau sunt errori afiseaza forma
if( empty($_POST) || !empty($errors) ) :
	
	if( !empty($errors['conexiune-imposibila']) ) echo $errors['conexiune-imposibila'];

?>

	<form method="post">
		<h3 class="form-section">Server details</h3>
		<div class="form-group">
			<label>DB Host</label>
			<input class="form-control" type="text" name="db-host" value="<?php echo $db_host; ?>"/>
			<?php if( !empty($errors['db-host']) ) echo $errors['db-host']; ?>
		</div>
		<div class="form-group">
			<label>DB Username</label>
			<input class="form-control" type="text" name="db-username" value="<?php echo $db_username; ?>"/>
			<?php if( !empty($errors['db-username']) ) echo $errors['db-username']; ?>
		</div>
		<div class="form-group">
			<label>DB Password</label>
			<input class="form-control" type="password" name="db-password" value="<?php echo $db_password; ?>"/>
			<?php if( !empty($errors['db-password']) ) echo $errors['db-password']; ?>
		</div>
		<div class="form-group">
			<label>DB Name</label>
			<input class="form-control" type="text" name="db-name" value="<?php echo $db_name; ?>"/>
			<?php if( !empty($errors['db-name']) ) echo $errors['db-name']; ?>
		</div>

		<h3 class="form-section">Site details</h3>
		<div class="form-group">
			<label>Site title</label>
			<input class="form-control" type="text" name="site-title" value="<?php echo $site_title; ?>"/>
			<?php if( !empty($errors['site-title']) ) echo $errors['site-title']; ?>
		</div>

		<h3 class="form-section">Administrator details</h3>
		<div class="form-group">
			<label>Administrator Username</label>
			<input class="form-control" type="text" name="admin-username" value="<?php echo $admin_username; ?>"/>
			<?php if( !empty($errors['admin-username']) ) echo $errors['admin-username']; ?>
		</div>
		<div class="form-group">
			<label>Administrator Password</label>
			<input class="form-control" type="password" name="admin-password" value="<?php echo $admin_password; ?>"/>
			<?php if( !empty($errors['admin-password']) ) echo $errors['admin-password']; ?>
		</div>
		<div class="form-group">
			<label>Administrator Email</label>
			<input class="form-control" type="email" name="admin-email" value="<?php echo $admin_email; ?>"/>
			<?php if( !empty($errors['admin-email']) ) echo $errors['admin-email']; ?>
		</div>

		<button type="submit" class="btn btn-primary">Trimite</button>
	</form>

<?php 
endif;

?>
</div>

	<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>

</body>
</html>