<?php

// System not installed, yet!
if( ! file_exists( dirname(__FILE__) . "/db-config.php" ) && basename( $_SERVER['REQUEST_URI'] ) !== 'install.php' ){
	header('Location: install.php');
	exit;
}

// System is installed!
require_once dirname(__FILE__) ."/db-config.php";
include dirname(__FILE__) ."/fields.php";


/*  Conectare la baza de date
------------------------------------------------*/
function conectare_la_db(){
	$connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	return $connection;
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

function get_page_url( $page_name ){
	return 'index.php?page=' .$page_name;
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
function site_title(){
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
	
	return $site_title;
}

/* Verificarea daca e admin(backend) sau nu
---------------------------------------*/
function is_admin(){
	$request_uri = explode('?', $_SERVER['REQUEST_URI']);
	$base = basename($request_uri[0], '.php');
	return 'admin' == $base;
}

function form_fields(){
	return array(

		'Datele asiguratului',

 		'statut_juridic' => array( 
 			'label' => 'Statutul juridic al asiguratului',
			'type' => 'radio',
			'options' => array(
				'fizica' => 'Persoana Fizica',
				'juridica' => 'Persoana Juridica',
			),
 		),
		'resedinta_sofer' => array( 
			'label' => 'Reședința șoferului/asiguratului',
			'type' => 'select',
			'options' => array(
				'mun_chisinau' => 'Mun. Chisinau',
				'mun_balti' => 'Mun. Balti',
				'alte_localitati' => 'Alte Localitati',
			),
		),
		'virsta_conducator' => array( 
			'label' => 'Vîrsta conducătorului auto (inclusiv persoanele admise la volan)', 
			'type' => 'select',
			'options' => array(
				'23-' => 'pina la 23 ani',
				'23+' => 'peste 23 ani',
			),
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
		),
		'pensionar' => array( 
			'label' => 'Sunteţi pensionar sau aveţi grad de invaliditate?',
			'type' => 'radio',
			'options' => array(
				'nu' => 'Nu',
				'da' => 'Da',
			),
		),
		'rca_contract_vechi' => array( 
			'label' => 'Ați avut încheiat un contract de asigurare RCA în ultimii 2 ani?', 
			'type' => 'radio',
			'options' => array(
				'nu' => 'Nu',
				'da' => 'Da',
			),
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
		),
		'putere_motor_tractor_rutier' => array( 
			'label' => 'Puterea motorului',
			'type' => 'select',
			'options' => array(
				'45-' => 'Pina la 45 c.p.,inclusiv',
				'46+' => 'De la 46 c.p., pina la 100 c.p., inclusiv',
				'100+' => 'Peste 100 c.p.',
			),
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
		),
		'capacitate_cilindrica_motocicleta' => array( 
			'label' => 'Capacitatea cilindrică',
			'type' => 'select',
			'options' => array(
				'300-' => 'Pina la 300 cm.3',
				'300+' => 'Peste 300 cm.3',
			),
		),
		'inmatriculat_tara' => array( 
			'label' => 'Vehicul înmatriculat în', 
			'type' => 'select',
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
			'type' => 'select',
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
		),
		'compania_asigurare' => array( 
			'label' => 'Alege Compania de Asigurare', 
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
		),

		'Formularul de comanda',

		'nume_prenume' => array( 
			'label' => 'Numele și Prenume',
			'type' => 'text',
		),
		'cod_personal' => array( 
			'label' => 'Codul personal',
			'type' => 'text',
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
		),
		'numar_inmatriculare_document' => array( 
			'label' => 'Numărul de înmatriculare al documentului',
			'type' => 'text',
		),
		'numar_inregistrare_vehicul' => array( 
			'label' => 'Numărul de înregistrare al autovehiculului', 
			'type' => 'text',
		),
		'an_fabricatie' => array( 
			'label' => 'An fabricație',
			'type' => 'number',
			'min' => 1900,
			'max' => date( 'Y' ),
		),
		'marca' => array( 
			'label' => 'Marca/Model', 
			'type' => 'text',
		),
		'tip_autovehicul' => array( 
			'label' => 'Tipul autovehiculului', 
			'type' => 'text',
		),
		'capacitate_cilindrica' => array( 
			'label' => 'Capacitatea cilindrică', 
			'type' => 'number',
		),
		'masa_proprie' => array( 
			'label' => 'Masa proprie', 
			'type' => 'number',
		),
		'masa_max_autorizata' => array( 
			'label' => 'Masa maximă autorizată',
			'type' => 'number',
		),
		'numar_locuri' => array( 
			'label' => 'Numărul de locuri', 
			'type' => 'number',
			'min' => 2,
			'max' => 20,
		),
		'numar_caroserie' => array( 
			'label' => 'Numărul caroseriei', 
			'type' => 'text',
		),
		'numar_motor' => array( 
			'label' => 'Numărul motorului',	 
			'type' => 'text',
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

calculator_rca();