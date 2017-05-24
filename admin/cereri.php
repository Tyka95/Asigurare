<?php
/* Sterge o pagina
------------------------------------------------*/
if( !empty($_GET['action']) && $_GET['action'] == 'delete' ){
	delete_cerere();
}
/* Vizualizeaza cererea
----------------------------*/
elseif( !empty($_GET['action']) && $_GET['action'] == 'view' && !empty($_GET['id']) ){
	if( $cerere = get_cerere_by_id( intval($_GET['id']) ) ){
	
		$form = form_fields();

		echo '<h2>Detalii cerere</h2>';
		echo '<table class="table">';

		foreach ($form as $field_id => $field) {
			
			// Is field
			if( is_array( $field ) && !empty($cerere[ $field_id ]) ){
				echo '<tr class="row">';

					echo '<td class="col-xs-6">';
						if( !empty( $field[ 'label' ] ) ){
							echo '<label>'. $field[ 'label' ] .'</label>';
						}
					echo '</td>';

					echo '<td class="col-xs-6">';
						echo form_value_label( $field_id, $cerere[ $field_id ] );
					echo '</td>';
				echo '</tr>';
			}

			// .. is section
			elseif( is_string( $field ) ){
				echo '</table>';
				echo '<h3 class="form-section">'. $field .'</h3>';
				echo '<table class="table">';
			}

		} // foreach
		
		echo '</table>';


		if( !empty($_POST['verificare_cerere']) ){
			if( $_POST['status'] == 'accepted' ){
				do_action( 'cerere_acceptata', intval($_GET['id']), $_POST['message'] );
				update_cerere_status( $_GET['id'], 'accepted' );
			}
			elseif( $_POST['status'] == 'rejected' ){
				do_action( 'cerere_respinsa', intval($_GET['id']), $_POST['message'] );
				update_cerere_status( $_GET['id'], 'rejected' );
			}
			else{
				update_cerere_status( $_GET['id'], 'pending' );
			}
		}


		echo '
		<h2>Verificare cerere</h2>

		<form action="" method="post">
			
			<div class="form-group">
				<label>Mesaj pentru utilizator</label>
				'. Field::textarea('message', '', array(
					'rows' => 10,
					'class' => 'admin-textarea',
				)) .'
			</div>

			<div class="form-group">
				<label>Satus</label>
				'. Field::nice_selector('status', $cerere['status'], array(
					'pending' => 'In asteptare',
					'accepted' => 'Aprobat',
					'rejected' => 'Respins',	
				)) .'
			</div>

			<button type="submit" class="btn btn-primary">Trimite</button>

			<input type="hidden" name="verificare_cerere" value="1" />

		</form>
		';

	}
}
else{
	/* Afiseaza cererile
	-------------------------*/
	$conn = conectare_la_db();

	$page = !empty($_GET['pag']) && intval($_GET['pag']) > 0 ? intval( $_GET['pag'] ) : 1;
	$limit = 10;
	$offset = ($limit*$page)-$limit;

	$query = "SELECT * FROM cereri ORDER BY id DESC LIMIT $limit OFFSET $offset";
	if( $result = $conn->query($query)) {
		$have_results = intval($result->num_rows) > 1;

		if( $have_results ){

			echo '<table class="table table-striped">';
			echo '<tr>
				<th>ID</th>
				<th>Date</th>
				<th>Detalii</th>
				<th>Status</th>
				<th>Opțiuni</th>
			</tr>';

			/* fetch associative array
			-------------------------------*/
			while($row = $result->fetch_assoc()){
				echo '<tr>';

					echo '<td>#'. $row['id'] .'</td>';

					echo '<td><a href="'. add_query_arg(array(
						'section' => 'cereri',
						'action' => 'view',
						'id' => $row['id'],
					)) .'" title="Vizualizeaza">';

					echo '<h4>'. $row['nume_prenume'] .'</h4>';
					echo '</a>';

					echo '<div class="text-muted">'. $row['email'] .' <br> '. $row['telefon'] .'</div>';

					echo '</td>';
				

					echo '<td>
					<strong>'. form_label( 'cod_personal' ) .'</strong>
					<div>'. $row['cod_personal'] .'</div>
					<strong>'. form_label( 'numar_inmatriculare_document' ) .'</strong>
					<div>'. $row['numar_inmatriculare_document'] .'</div>
					<strong>'. form_label( 'numar_inregistrare_vehicul' ) .'</strong>
					<div>'. $row['numar_inregistrare_vehicul'] .'</div>
					</td>';

					if( 'accepted' == $row['status'] ){
						$status = 'success';
						$status_name = 'Aprobat';
					}
					elseif( 'rejected' == $row['status'] ){
						$status = 'danger';
						$status_name = 'Respins';
					}
					else{
						$status = 'default';
						$status_name = 'In asteptare';
					}

					echo '<td> <span class="label label-'. $status .'">'. $status_name .'</span></td>';

					echo '<td style="min-width: 100px;">
						<a class="btn btn-success btn-xs" href="'. add_query_arg(array(
							'section' => 'cereri',
							'action' => 'view',
							'id' => $row['id'],
						)) .'" title="Vizualizeaza"><span class="glyphicon glyphicon-eye-open"></span></a>
						<a class="btn btn-danger btn-xs" href="'. add_query_arg(array(
							'section' => 'cereri',
							'action' => 'delete',
							'id' => $row['id'],
						)) .'" title="Sterge" data-confirm-delete="Sunteti sigur ca doriti sa stergeti aceasta cerere?"><span class="glyphicon glyphicon-trash"></span></a>
					</td>';
					
				echo '</tr>';
			}
		}
		else{
			echo '<div class="alert alert-warning">Nu sunt rezultate</div>';
		}
		//eliberam memoria din $result
		$result->free();

	}
	
	$conn->close();

	echo '</table>';

	echo '<div class="buttons-group">';
		if( $page > 1 ){
			echo '<a class="btn btn-default pull-left" href="'. add_query_arg( array('pag' => $page-1) ) .'">Pagina anterioara</a>';
		}

		if( $have_results ){
			echo '<a class="btn btn-default pull-right" href="'. add_query_arg( array('pag' => $page+1) ) .'">Pagina urmatoare</a>';
		}
	echo '</div>';
}