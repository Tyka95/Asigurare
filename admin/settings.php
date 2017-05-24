<?php 
$site_title = '';
$site_email = '';

if( !empty($_POST['save_general_settings']) ){
	
	$errors = array();


	// Site title
	if( empty($_POST['site_title']) ){
		$errors[] = 'Completati câmpul "Titlul site-ului".';
		$_POST['site_title'] = '';
	}
	else{
		$_POST['site_title'] = htmlspecialchars( $_POST['site_title'] );
	}

	// Site email
	if( empty($_POST['site_email']) ){
		$errors[] = 'Completati câmpul "Email-ul principal al site-ului".';
		$_POST['site_email'] = '';
	}
	else{
		$_POST['site_email'] = htmlspecialchars( $_POST['site_email'] );
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
		unset( $_POST['save_general_settings'] );

		update_option( 'site_title', $_POST['site_title'] );
		update_option( 'site_email', $_POST['site_email'] );

		echo '<div class="alert alert-success">Datele au fost salvate cu succes.</div>';
	}

	extract($_POST);
}

?>
<form action="" method="post">
	
	<div class="form-group">
		<label>Titlul site-ului</label>
		<?php echo Field::text('site_title', get_option( 'site_title', $site_title ) ); ?>
	</div>

	<div class="form-group">
		<label>Email-ul principal al site-ului</label>
		<?php echo Field::text('site_email', get_option( 'site_email', $site_email ) ); ?>
	</div>

	<button type="submit" class="btn btn-primary">Salveaza</button>

	<input type="hidden" name="save_general_settings" value="1" />

</form>