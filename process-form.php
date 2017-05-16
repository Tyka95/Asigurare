<?php
// Utilizatorul a trimis datele
if( !empty($_POST['form_date_asigurat']) ) :
	$data = $_POST;

	$nume = htmlspecialchars($data['nume_prenume']);
	$cod_personal = htmlspecialchars($data['cod_personal']);
	$nr_doc = htmlspecialchars($data['numar_inmatriculare_document']);
	$nr_vehicul = htmlspecialchars($data['numar_inregistrare_vehicul']);

	/*Stergem datele din array $data
	----------------------------------*/
	unset($data['nume_prenume']);
	unset($data['cod_personal']);
	unset($data['numar_inmatriculare_document']);
	unset($data['numar_iregistrare_vehicul']);

	$datele = htmlspecialchars( serialize( $data ) );

	/*Insert data to BD*/

	$conn = conectare_la_db();

	if(!$conn){
		echo '<div class="alert alert-danger">Conexiune imposibila la baza de date !!!</div>';
	}

	$sql = "INSERT INTO cereri 
			(nume_prenume,cod_personal, numar_inmatriculare_document, numar_inregistrare_vehicul, status, datele)
			 VALUES ('$nume','$cod_personal','$nr_doc','$nr_vehicul','pending','$datele')";

	$retval = mysqli_query( $conn, $sql );

	if( ! $retval ) {
		echo '<pre>';
		print_r( $retval );
		echo '</pre>';
			echo '<div class="alert alert-danger">Nu putem introduce datele in BD</div>';
	}
	else{
		echo '<div class="alert alert-success">Datele au fost introduse cu succes. Va multumim!</div>';
	}

	mysqli_close($conn);

endif;