<?php 
$host       = 'smtp.gmail.com';
$username   = $password = $from = $from_name = '';
$encryption = 'tls';
$port       = 587;

if( !empty($_POST['save_mail_settings']) ){
	
	$errors = array();

	if( empty($_POST['host']) ){
		$errors[] = 'Completati câmpul "Host".';
		$_POST['host'] = '';
	}
	else{
		$_POST['host'] = htmlspecialchars( $_POST['host'] );
	}

	if( empty($_POST['username']) ){
		$errors[] = 'Completati câmpul "Username".';
		$_POST['username'] = '';
	}
	else{
		$_POST['username'] = htmlspecialchars( $_POST['username'] );
	}

	if( empty($_POST['password']) ){
		$errors[] = 'Completati câmpul "Password".';
		$_POST['password'] = '';
	}
	else{
		$_POST['password'] = htmlspecialchars( $_POST['password'] );
	}

	if( empty($_POST['encryption']) ){
		$errors[] = 'Completati câmpul "Encryption".';
		$_POST['encryption'] = 'tls';
	}
	elseif( $_POST['encryption'] == 'ssl' ||  $_POST['encryption'] == 'tls' ){
		$_POST['encryption'] = htmlspecialchars( $_POST['encryption'] );
	}

	if( empty($_POST['port']) ){
		$errors[] = 'Completati câmpul "Port".';
		$_POST['port'] = '';
	}
	else{
		$_POST['port'] = intval( $_POST['port'] );
	}

	if( empty($_POST['from']) ){
		$errors[] = 'Completati câmpul "From".';
		$_POST['from'] = '';
	}
	else{
		$_POST['from'] = htmlspecialchars( $_POST['from'] );
	}

	if( empty($_POST['from_name']) ){
		$errors[] = 'Completati câmpul "From name".';
		$_POST['from_name'] = '';
	}
	else{
		$_POST['from_name'] = htmlspecialchars( $_POST['from_name'] );
	}


	if( !empty($errors) ){
		echo '<div class="alert alert-danger">';
			echo '<h4>Please fix the following errors:</h4>';
			foreach ($errors as $error) {
				echo '<div>'. $error .'</div>';
			}
		echo '</div>';
	}
	else{
		unset( $_POST['save_mail_settings'] );
		update_option( 'mail_settings', $_POST );
		echo '<div class="alert alert-success">Datele au fost salvate cu succes.</div>';
	}

	extract($_POST);

}
else{
	$data = get_option( 'mail_settings' );
	if( isset( $data ) ){
		extract( $data );
	}
}

?>
<form action="" method="post">
	<div class="form-group">
		<label>Host</label>
		<?php echo Field::text('host', $host); ?>
	</div>

	<div class="form-group">
		<label>Username</label>
		<?php echo Field::text('username', $username); ?>
	</div>

	<div class="form-group">
		<label>Password</label>
		<?php echo Field::text('password', $password, 'password'); ?>
	</div>

	<div class="form-group">
		<label>Encryption</label>
		<?php echo Field::select('encryption', $encryption, array(
			'tls' => 'TLS',
			'ssl' => 'SSL',
		)); ?>
	</div>

	<div class="form-group">
		<label>Port</label>
		<?php echo Field::text('port', $port); ?>
	</div>

	<div class="form-group">
		<label>From</label>
		<?php echo Field::text('from', $from); ?>
	</div>

	<div class="form-group">
		<label>From name</label>
		<?php echo Field::text('from_name', $from_name); ?>
	</div>

	<button type="submit" class="btn btn-primary">Trimite</button>

	<input type="hidden" name="save_mail_settings" value="1" />

</form>