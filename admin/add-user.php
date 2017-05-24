<?php 
$username = '';
$password ='';
$email ='';
$type = '';
$errors = array();
$data = false;

// Daca forma nu a fost trimisa si avem un ID, atunci editam utilizatorul
if( empty($_POST) && !empty($_GET['id']) ){
	$data = get_user_by_id( intval($_GET['id']) );
}

// Primeste detaliile din post
if( empty($data) ){
	$data = $_POST;
}

if( !empty($data['username']) ){
	$username = strip_tags($data['username']);
}
else{
	$errors[] = 'Introduceti numele utilizatorului.';
}

if( !empty($data['password']) ){
	$password = strip_tags($data['password']);
}
else{
	if( empty($_POST['action']) && ( !empty($_POST['action']) && 'edit' !== $_POST['action'] ) ){
		$errors[] = 'Introduceti parola utilizatorului.';
	}
}

if( !empty($data['email']) ){
	$email = strip_tags($data['email']);
}
else{
	$errors[] = 'Introduceti emailul utilizatorului.';
}

if( isset($data['type']) ){
	$type = strip_tags($data['type']);
}
else{
	$errors[] = 'Introduceti tipul uerului.';
}

$user_created = false;

// Form a fost trimisa
if(!empty($_POST)){

	if( empty($errors) && !empty($_POST['action']) ){
		if( 'add' == $_POST['action'] ){
			$user_created = add_user( $username, $password, $email, $type );
			
			if( !empty($user_created['success']) ){
				echo '<div class="alert alert-success">'. $user_created['success'] .'</div>';
			}
			else{
				echo '<div class="alert alert-danger">'. $user_created['error'] .'</div>';
			}
		}
		elseif('edit' == $_POST['action'] ){
			$user_edited = edit_user( intval($_GET['id']), $username, $password, $email, $type );
			
			if( !empty($user_edited['success']) ){
				echo '<div class="alert alert-success">'. $user_edited['success'] .'</div>';
			}
			else{
				echo '<div class="alert alert-danger">'. $user_edited['error'] .'</div>';
			}
		}
	}

	if( !empty($errors) ){
		echo '<div class="alert alert-danger"><ul>';
		foreach ($errors as $error) {
			echo '<li>'. $error .'</li>';
		}
		echo '</ul></div>';
	}

}

// Daca forma a fost trimisa si avem un mesaj de sucess, nu afisam forma de mai jos.
if( !empty( $user_created ) && !empty($user_created['success']) ){
	return false;
}

// Afisam forma.
?>
<form  method="post">

	<div class="form-group">
		<label>Nume</label>
		<?php echo Field::text('username', $username); ?>
	</div>

	<div class="form-group">
		<label>Parola</label>
		<?php echo Field::text('password', '', 'password'); ?>
	</div>

	<div class="form-group">
		<label>Email</label>
		<?php echo Field::text('email', $email,'email'); ?>
	</div>

	<div class="form-group">
		<label>Tip</label>
		<?php echo Field::radio( 'type', $type, array(
			'moderator' => 'Moderator',
			'admin'  => 'Administrator',
		)); ?>
	</div>
	<?php echo '<div class="alert alert-warning">Fiti atent la tipul utilizatorului ! </div>';?>

	<button type="submit" class="btn btn-primary">Trimite</button>

	<?php 
		if( !empty($_GET['id']) ){
			$action_value = 'edit';
		}
		else{
			$action_value = 'add';
		}
	?>
	<input type="hidden" name="action" value="<?php echo $action_value; ?>" />

</form>