<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title><?php echo get_option('site_title') .' - '. site_title( true ); ?></title>
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap-theme.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="icon" href="img/asigurare.png">
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
				<h1 class="logo"><a href="index.php"><img src="img/logo2.png"></a></h1>
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
							$link = get_page_url( $id );
						}

						// Marcheaza pagina activa
						$active = !empty($_GET['page']) && $_GET['page'] == $id ? 'active' : '';

						// Daca suntem pe pagina pricipala
						if( empty( $_GET['page'] ) && empty( $id ) && ! is_admin() ){
							$active = 'active';
						}

						// Afiseaza linkul
						echo '<li><a href="'. $link .'" class="'. $active .'">'. $label .'</a></li>';
					}

					?>
				</ul>
			</div>
		</div>
		<?php 
			if( ! is_home() ){
				echo '<div class="page-title"><h1><span>'. site_title() .'</span></h1></div>';
			}
			else{
				// Maybe a slider?
			}
		?>
	</div>
</div>


<div class="site">