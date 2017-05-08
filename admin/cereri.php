<?php

$conn = conectare_la_db();
if( $conn->connect_errno ){
	printf("Nu se poate conecta la BD", $conn->connect_errno);
exit();
}	
$query = "SELECT  * FROM asi_auto";

echo '<table class="table table-striped">';
echo '<tr>
	<td>ID</td>
	<td>'. form_label( 'nume_prenume' ) .'</td>
	<td>'. form_label( 'cod_personal' ) .'</td>
	<td>'. form_label( 'numar_inmatriculare_document' ) .'</td>
	<td>'. form_label( 'numar_inregistrare_vehicul' ) .'</td>
	<td>Editare</td>
	<td>Eliminare</td>
</tr>';

if( $result = $conn->query($query)) {
	
	/* fetch associative array
	-------------------------------*/
	while($row = $result->fetch_assoc()){
		echo '<tr>';
			foreach ($row as $key => $value) {
				if( 'datele' == $key){
					// $date = unserialize( htmlspecialchars_decode ($value) );
					// foreach ($date as $k => $v) { 
					// 	echo " <table><th>". form_label( $k ) .":</th> <td>{$v}</td></table> <br />";
					// }
				}
				else{
					// echo "<table> <th>".form_label($key).":</th> <td> {$value}</td></table><br />";
					echo '<td>'. $value .'</td>';
				}

			}
			echo '<td><a href="?section=edit-cerere&id='. $row['id'] .'">Editeaza</a></td>';
			echo '<td><a href="?section=cereri&action=sterge-cerere&id='. $row['id'] .'">Sterge</a></td>';
		echo '</tr>';
	}
	//eliberam memoria din $result
	$result->free();

}
$conn->close();

echo '</table>';