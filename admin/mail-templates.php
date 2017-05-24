<?php 
$cerere_inregistrata_subject = 'O noua cerere a fost inregistrata';
$cerere_inregistrata_message = '<p>Salut!</p>
		 <p>Aveti o noua cerere in asteptare pe {SITE_URL}</p>
		 <p><small>Acesta este un mesaj generat automat!</small></p>
		';
$cerere_inregistrata_email = get_option( 'site_email' );


$cerere_acceptata_subject = 'Cerere acceptată';
$cerere_acceptata_message = '<p>Salut!</p>
		 <p>Cererea dvs. a fost acceptată.</p>
		 <p>{REVIEW_MESSAGE}</p>
		 <p>Vă multumim.</p>
		 <p>Echipa {SITE_TITLE}</p>
		 <p>{SITE_URL}</p>
		';


$cerere_respinsa_subject = 'Cerere respinsă';
$cerere_respinsa_message = '<p>Salut!</p>
		 <p>Ne pare rău dar cererea dvs. a fost respinsă.</p>
		 <p>{REVIEW_MESSAGE}</p>
		 <p>La revedere.</p>
		 <p>Echipa {SITE_TITLE}</p>
		 <p>{SITE_URL}</p>
		';

if( !empty($_POST['save_mail_templates']) ){
	
	$errors = array();

	//Inregistrare
	if( empty($_POST['cerere_inregistrata_email']) ){
		$errors[] = 'Completati câmpul "Cerere inregistrata: Email".';
		$_POST['cerere_inregistrata_email'] = '';
	}
	else{
		$_POST['cerere_inregistrata_email'] = htmlspecialchars( $_POST['cerere_inregistrata_email'] );
	}

	if( empty($_POST['cerere_inregistrata_subject']) ){
		$errors[] = 'Completati câmpul "Cerere inregistrata: Subiect".';
		$_POST['cerere_inregistrata_subject'] = '';
	}
	else{
		$_POST['cerere_inregistrata_subject'] = htmlspecialchars( $_POST['cerere_inregistrata_subject'] );
	}

	if( empty($_POST['cerere_inregistrata_message']) ){
		$errors[] = 'Completati câmpul "Cerere inregistrata: Mesaj".';
		$_POST['cerere_inregistrata_message'] = '';
	}
	else{
		$_POST['cerere_inregistrata_message'] = $_POST['cerere_inregistrata_message'];
	}

	//Acceptare
	if( empty($_POST['cerere_acceptata_subject']) ){
		$errors[] = 'Completati câmpul "Cerere acceptata: Subiect".';
		$_POST['cerere_acceptata_subject'] = '';
	}
	else{
		$_POST['cerere_acceptata_subject'] = htmlspecialchars( $_POST['cerere_acceptata_subject'] );
	}

	if( empty($_POST['cerere_acceptata_message']) ){
		$errors[] = 'Completati câmpul "Cerere acceptata: Email".';
		$_POST['cerere_acceptata_message'] = '';
	}
	else{
		$_POST['cerere_acceptata_message'] = $_POST['cerere_acceptata_message'];
	}


	//Respinge
	if( empty($_POST['cerere_respinsa_subject']) ){
		$errors[] = 'Completati câmpul "Cerere respinsa: Subiect".';
		$_POST['cerere_respinsa_subject'] = '';
	}
	else{
		$_POST['cerere_respinsa_subject'] = htmlspecialchars( $_POST['cerere_respinsa_subject'] );
	}

	if( empty($_POST['cerere_respinsa_message']) ){
		$errors[] = 'Completati câmpul "Cerere respinsa: Email".';
		$_POST['cerere_respinsa_message'] = '';
	}
	else{
		$_POST['cerere_respinsa_message'] = $_POST['cerere_respinsa_message'];
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
		unset( $_POST['save_mail_templates'] );
		update_option( 'mail_templates', $_POST );
		echo '<div class="alert alert-success">Datele au fost salvate cu succes.</div>';
	}

	extract($_POST);

}
else{
	$data = get_option( 'mail_templates' );
	if( isset( $data ) ){
		extract( $data );
	}
}

?>
<form action="" method="post">
	
	<div class="alert alert-info">Urmatoarele șabloane sunt folosite in diferite circumstante pentru notificarea prin email.</div>
	
	<p>&nbsp;</p>

	<h3>Cerere inregistrata:</h3>

	<div class="form-group">
		<label>Subiect:</label>
		<?php echo Field::text('cerere_inregistrata_subject', $cerere_inregistrata_subject); ?>
	</div>

	<div class="form-group">
		<label>Mesaj:</label>
		<?php echo Field::textarea('cerere_inregistrata_message', $cerere_inregistrata_message, array(
			'rows' => 15,
			'class' => 'admin-textarea',
		)); ?>
	</div>
	
	<div class="form-group">
		<label>Trimite email la:</label>
		<?php echo Field::text('cerere_inregistrata_email', $cerere_inregistrata_email); ?>
	</div>


	<p>&nbsp;</p>
	
	<h3>Cerere acceptată:</h3>

	<div class="form-group">
		<label>Subiect:</label>
		<?php echo Field::text('cerere_acceptata_subject', $cerere_acceptata_subject); ?>
	</div>

	<div class="form-group">
		<label>Mesaj:</label>
		<?php echo Field::textarea('cerere_acceptata_message', $cerere_acceptata_message, array(
			'rows' => 15,
			'class' => 'admin-textarea',
		)); ?>
	</div>
	

	<p>&nbsp;</p>
	
	<h3>Cerere respinsa:</h3>

	<div class="form-group">
		<label>Subiect:</label>
		<?php echo Field::text('cerere_respinsa_subject', $cerere_respinsa_subject); ?>
	</div>

	<div class="form-group">
		<label>Mesaj:</label>
		<?php echo Field::textarea('cerere_respinsa_message', $cerere_respinsa_message, array(
			'rows' => 15,
			'class' => 'admin-textarea',
		)); ?>
	</div>
	


	<button type="submit" class="btn btn-primary">Salveaza</button>

	<input type="hidden" name="save_mail_templates" value="1" />


	<p>&nbsp;</p>
	
	<h3>Coduri scurte:</h3>
	<table class="table">
		<tr>
			<th>Cod:</th>
			<th>Descriere:</th>
		</tr>
		<tr>
			<td><code>{SITE_URL}</code></td>
			<td>Adresa curenta a site-ului</td>
		</tr>
		<tr>
			<td><code>{SITE_TITLE}</code></td>
			<td>Titlul site-ului</td>
		</tr>
		<tr>
			<td><code>{REVIEW_MESSAGE}</code></td>
			<td>Mesajul indrodus in timpul verificarii unei cereri. Acest cod este disponibil doar in șabloanele "Cerere acceptată" și "Cerere respinsă".</td>
		</tr>
	</table>


</form>