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

echo '<div class="row">';
foreach ($form as $field_id => $field) {
	$grid = !empty($field['grid']) ? ' '. $field['grid'] : ' col-xs-12';
	
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


		echo '<div class="form-group'. $grid .'"'. $show_if .'>';

		if( !empty( $field[ 'label' ] ) ){
			echo '<label>';
			echo $field[ 'label' ];
			
			if( !empty( $field[ 'img_tip' ] ) ){
				echo '&nbsp;<span 
					class="glyphicon glyphicon-question-sign" 
					data-toggle="popover" 
					title="'. htmlspecialchars( $field[ 'label' ] ) .'" 
					data-img-tip="'. htmlspecialchars( $field_id ) .'"
				></span>';
			}
			
			echo '</label>';
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
					echo Field::select( $field_id, $value, $field);
				break;
			
			case 'radio':
					echo Field::radio( $field_id, $value, $field);
				break;
			
			case 'nice_selector':
					echo Field::nice_selector( $field_id, $value, $field);
				break;
			
			case 'persoane_admin_la_volan':
					echo Field::persoane_admin_la_volan( $field_id, $value);
				break;
			
			default:
				echo '<div class="alert alert-danger">Invalid field type!</div>';
				break;

		} // switch

		echo '</div>';

	}

	// .. is section
	elseif( is_string( $field ) ){
		echo '<h3 class="form-section'. $grid .'">'. $field .'</h3>';
	}
	
	if( !empty($field['clear_row']) ){
		echo '</div><div class="row">';
	}

} // foreach

echo '</div>'; //row;

?>

	<div class="form-group">
		<input type="hidden" name="form_date_asigurat" value="1" />
		<input type="submit" value="Trimite" class="btn btn-primary" />
	</div>

</form>