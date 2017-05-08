<?php

$conn = conectare_la_db();
if( $conn->connect_errno ){
	printf("Nu se poate conecta la BD", $conn->connect_errno);
exit();
}	
$query = "SELECT  * FROM asi_auto";

if( $result = $conn->query($query)) {
	
	/* fetch associative array
	-------------------------------*/
	while($row = $result->fetch_assoc()){
		foreach ($row as $key => $value) {
			if( 'datele' == $key){
				$date = unserialize( htmlspecialchars_decode ($value) );
				foreach ($date as $k => $v) { 
					echo " <table><th>". form_label( $k ) .":</th> <td>{$v}</td></table> <br />";
				}
			}
			else{
				echo "<table> <th>".form_label($key).":</th> <td> {$value}</td></table><br />";
			}

		}
		echo '<hr>';
	}
	//eliberam memoria din $result
	$result->free();

}
$conn->close();