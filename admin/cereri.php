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

	$query = "SELECT  * FROM cereri";

	echo '<table class="table table-striped">';
	echo '<tr>
		<th>ID</th>
		<th>'. form_label( 'nume_prenume' ) .'</th>
		<th>'. form_label( 'cod_personal' ) .'</th>
		<th>'. form_label( 'numar_inmatriculare_document' ) .'</th>
		<th>'. form_label( 'numar_inregistrare_vehicul' ) .'</th>
		<th>Status</th>
		<th>Opțiuni</th>
	</tr>';

	if( $result = $conn->query($query)) {
		
		/* fetch associative array
		-------------------------------*/
		while($row = $result->fetch_assoc()){
			echo '<tr>';
				foreach ($row as $key => $value) {
					if( 'nume_prenume' == $key){
						echo '<td><a href="'. add_query_arg(array(
							'section' => 'cereri',
							'action' => 'view',
							'id' => $row['id'],
						)) .'" title="Vizualizeaza">'. $value .'</a></td>';
					}
					elseif( 'status' == $key){
						if( 'accepted' == $value ){
							$status = 'success';
							$status_name = 'Aprobat';
						}
						elseif( 'rejected' == $value ){
							$status = 'danger';
							$status_name = 'Respins';
						}
						else{
							$status = 'default';
							$status_name = 'In asteptare';
						}

						echo '<td> <span class="label label-'. $status .'">'. $status_name .'</span></td>';
					}

					// Excludem coloana 'datele' deoarece e serializat.
					elseif( 'datele' !== $key){
						echo '<td>'. $value .'</td>';
					}

				}
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
		//eliberam memoria din $result
		$result->free();

	}
	$conn->close();

	echo '</table>';
}