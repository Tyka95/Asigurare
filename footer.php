</div>


<div class="footer">
	<div class="footer-in">
		<div class="row">
			<div class="col-xs-6">
				<div class="copyright">Copyright &copy; <?php date( 'Y' ); ?>. All rights reserved</div>
			</div>
			
			<div class="col-xs-6">
				<ul class="footer-menu">
					
					<?php 

					$meniu = array(
						'admin.php' => 'Admin',
					);
					
					$pagini = toate_paginile();
					if( !empty($pagini) ){
						foreach ($pagini as $pagina) {
							if( 'jos' == $pagina[ 'meniu' ] ){
								$meniu[ $pagina[ 'url_text' ] ] = $pagina[ 'titlu' ];
							}
						}
					}

					foreach ($meniu as $id => $label) {
						
						if( 'admin.php' == $id ){
							$link = 'admin.php';
						}
						else{
							$link = get_page_url( $id );
						}

						// Marcheaza pagina activa
						$active = !empty($_GET['page']) && $_GET['page'] == $id ? 'active' : '';

						// Daca suntem pe pagina pricipala
						if( is_admin() && 'admin.php' == $id ){
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

	<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>

</body>
</html>