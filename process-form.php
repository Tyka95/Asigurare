<?php
// Utilizatorul a trimis datele
if( !empty($_POST['form_date_asigurat']) ) :
	$data = $_POST;

	$data['nume_prenume'] = htmlspecialchars($data['nume_prenume']);
	$data['cod_personal'] = htmlspecialchars($data['cod_personal']);
	$data['numar_inmatriculare_document'] = htmlspecialchars($data['numar_inmatriculare_document']);
	$data['numar_inregistrare_vehicul'] = htmlspecialchars($data['numar_inregistrare_vehicul']);

	/*Insert data to BD*/

	$conn = conectare_la_db();

	if(!$conn){
		echo '<div class="alert alert-danger">Conexiune imposibila la baza de date !!!</div>';
	}

	// Pregatim coloanele principale
	$nume = htmlspecialchars($data['nume_prenume']);
	$cod_personal = htmlspecialchars($data['cod_personal']);
	$nr_doc = htmlspecialchars($data['numar_inmatriculare_document']);
	$nr_vehicul = htmlspecialchars($data['numar_inregistrare_vehicul']);

	$other_data = $data;
	unset($other_data['nume_prenume']);
	unset($other_data['cod_personal']);
	unset($other_data['numar_inmatriculare_document']);
	unset($other_data['numar_iregistrare_vehicul']);

	$datele = htmlspecialchars( serialize( $other_data ) );


	$sql = "INSERT INTO cereri 
			(nume_prenume,cod_personal, numar_inmatriculare_document, numar_inregistrare_vehicul, status, datele)
			 VALUES ('$nume','$cod_personal','$nr_doc','$nr_vehicul','pending','$datele')";

	$retval = mysqli_query( $conn, $sql );

	if( ! $retval ) {
		echo '<div class="alert alert-danger">Nu putem introduce datele in BD</div>';
	}
	else{
		echo '<div class="alert alert-success">Datele au fost introduse cu succes. Va multumim!</div>';
		do_action( 'cerere_inregistrata_cu_success', $data );
	}

	mysqli_close($conn);

endif;