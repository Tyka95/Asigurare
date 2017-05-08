<?php
require_once dirname(__FILE__) . "/functions.php";

get_header();

if( !empty($_GET[ 'page' ]) ){
	$page_name = $_GET[ 'page' ];

	// Daca fisierul exista il includem
	if( file_exists( dirname(__FILE__) . "/{$page_name}.php" ) ){
		include dirname(__FILE__) . "/{$page_name}.php";
	}

	// ... daca nu exista, cautam pagina in baza de date
	else{
		$pagina = primire_pagina( $page_name );
		if( !empty($pagina['continut']) ){
			echo $pagina['continut'];
		}
	}

}
else{
	include dirname(__FILE__) . "/acasa.php";
}

get_footer();