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
<?php 
	if( is_admin() ){
	?>
	
	<script src="js/tinymce/tinymce.min.js"></script>
	<script>tinymce.init({ selector:'.admin-textarea' });</script>
	
	<?php
	}
?>
</head>
<body>
<div class="header">
	<div class="header-in">
		<div class="row">
			<div class="col-xs-5">
				<h1 class="logo"><a href="index.php">Asigurare RCA</a></h1>
			</div>
			<div class="col-xs-7">
				<ul class="menu">
					<?php 

					$meniu = array(
						'' => 'Acasa',
						'asigurare' => 'Asigurare',
					);

					$pagini = toate_paginile();
					if( !empty($pagini) ){
						foreach ($pagini as $pagina) {
							if( 'sus' == $pagina[ 'meniu' ] ){
								$meniu[ $pagina[ 'url_text' ] ] = $pagina[ 'titlu' ];
							}
						}
					}

					foreach ($meniu as $id => $label) {
						
						if( empty( $id ) ){
							$link = 'index.php';
						}
						else{
							$link = 'index.php?page=' .$id;
						}

						// Marcheaza pagina activa
						$active = !empty($_GET['page']) && $_GET['page'] == $id ? 'active' : '';

						// Daca suntem pe pagina pricipala
						if( empty( $_GET['page'] ) && empty( $id ) ){
							$active = 'active';
						}

						// Afiseaza linkul
						echo '<li><a href="'. $link .'" class="'. $active .'">'. $label .'</a></li>';
					}

					?>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="site">

<?php 
	if( ! is_admin() ){
		echo '<div class="page-title"><h1>'. site_title() .'</h1></div>'; 
	}
?>