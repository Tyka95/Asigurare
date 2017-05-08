<?php
/* Sterge o pagina
------------------------------------------------*/
if( !empty($_GET['action']) && $_GET['action'] == 'sterge-pagina' && !empty($_GET['id']) ){
	$conn     = conectare_la_db();
	$id       = mysqli_real_escape_string( $conn, strip_tags($_GET['id']) );
	$sql      = "DELETE FROM pages WHERE id = '$id'";
	if( mysqli_query($conn, $sql) ){
		echo '<div class="site-notice">Pagina a fost stearsa cu succes.</div>';
	}
	else{
		echo '<div class="site-notice red">Eroare la stergerea unei pagini;' . mysqli_error() .'</div>';
	}
	mysqli_close($conn);
}

/* Afiseaza toate paginile
------------------------------------------------*/
$pagini = toate_paginile();
echo '<table class="table table-striped">';
echo '<tr>
	<td>Titlul</td>
	<td>Meniul</td>
	<td>Data adaugarii</td>
	<td></td>
	<td></td>
</tr>';

if( is_array($pagini) ){
	foreach ($pagini as $pagina) {
		echo '<tr>';
		echo '<td>'. $pagina['titlu'] .'</td>';
		echo '<td>'. $pagina['meniu'] .'</td>';
		echo '<td>'. $pagina['data_adaugarii'] .'</td>';
		echo '<td><a href="?section=adauga-pagina&id='. $pagina['id'] .'">Editeaza</a></td>';
		echo '<td><a href="?section=pagini&action=sterge-pagina&id='. $pagina['id'] .'">Sterge</a></td>';
		echo '</tr>';
	}
}
echo '</table>';