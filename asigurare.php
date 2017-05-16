<?php 
/*
-------------------------------------------------------------------------------
Proceseaza forma daca a fost trimisa de utilizator.
-------------------------------------------------------------------------------
*/
include __DIR__ . '/process-form.php';
?>

<form method="post">

<?php 
/*
-------------------------------------------------------------------------------
Afiseaza forma
-------------------------------------------------------------------------------
*/
$form = form_fields();

foreach ($form as $field_id => $field) {
	
	// Is field
	if( is_array( $field ) ){

		$show_if = '';
		if( !empty($field['show_if']) ){
			$conditions = (array) $field['show_if'];
			foreach ($conditions as $condition) {
				$condition = explode('::', $condition);
				$show_if .= ' data-show-if-'. htmlspecialchars( $condition[0] ) .'="' . htmlspecialchars( $condition[1] ) .'"';
			}
		}

		echo '<div class="form-group"'. $show_if .'>';

		if( !empty( $field[ 'label' ] ) ){
			echo '<label>'. $field[ 'label' ] .'</label>';
		}

		if( !empty($_POST['form_date_asigurat']) && isset($_POST[ $field_id ]) ){
			$value = $_POST[ $field_id ];
		}
		else{
			$value = isset( $field['default'] ) ? $field['default'] : '';
		}
		
		switch ($field[ 'type' ]) {
			
			case 'text';
				echo Field::text( $field_id, $value );
				break;

			case 'number':
					echo Field::number( $field_id, $value, $field );
				break;
			
			case 'select':
					echo Field::select( $field_id, $value, $field[ 'options' ]);
				break;
			
			case 'radio':
					echo Field::radio( $field_id, $value, $field[ 'options' ]);
				break;
			
			default:
				echo '<div class="alert alert-danger">Invalid field type!</div>';
				break;

		} // switch

		echo '</div>';
	}

	// .. is section
	elseif( is_string( $field ) ){
		echo '<h3 class="form-section">'. $field .'</h3>';
	}

} // foreach

?>

	<div class="form-group">
		<input type="hidden" name="form_date_asigurat" value="1" />
		<input type="submit" value="Trimite" class="btn btn-primary" />
	</div>

</form>