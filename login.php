<?php global $mesaj; ?>
<div class="login-form">
	<?php 
		if(!empty($mesaj)){
			echo $mesaj;
		}
	?>
	<form method="post" action="admin.php">
		
		<div class="form-group">
			<label>Nume Utilizator:</label>
			<?php echo Field::text('username', ''); ?>
		</div>

		<div class="form-group">
			<label>Parola:</label>
			<?php echo Field::text('password', '', 'password'); ?>
		</div>
		
		<button type="submit" class="btn btn-primary">Trimite</button>

		<input type="hidden" name="authentication_form" value="1" />
	
	</form>

</div>