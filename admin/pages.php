<?php
/* Sterge o pagina
------------------------------------------------*/
if( !empty($_GET['action']) && $_GET['action'] == 'delete' ){
	delete_page();
}

/* Afiseaza toate paginile
------------------------------------------------*/
$pagini = toate_paginile();
echo '<table class="table table-striped">';
echo '<tr>
	<th>Titlul</th>
	<th>Meniul</th>
	<th>Data adaugarii</th>
	<th>Opțiuni</th>
</tr>';

if( is_array($pagini) ){
	foreach ($pagini as $pagina) {
		echo '<tr>';
		echo '<td>'. $pagina['titlu'] .'</td>';
		echo '<td>'. $pagina['meniu'] .'</td>';
		echo '<td>'. $pagina['data_adaugarii'] .'</td>';
		
		echo '<td>
		<a class="btn btn-success btn-xs" href="'. get_page_url( $pagina['url_text'] ) .'" target="_blank" title="View"><span class="glyphicon glyphicon-eye-open"></span></a></a>
		<a class="btn btn-primary btn-xs" href="'. add_query_arg( array( 
			'section' => 'add-page',
			'id' => $pagina['id'],
		) ) .'" title="Editeaza"><span class="glyphicon glyphicon-pencil"></span></a></a>

		<a class="btn btn-danger btn-xs" href="'. add_query_arg( array( 
			'action' => 'delete',
			'id' => $pagina['id'],
		) ) .'" data-confirm-delete="Sunteti sigur ca doriti sa stergeti pagina: '. $pagina['titlu'] .'" title="Sterge"><span class="glyphicon glyphicon-trash"></span></a></a></td>';

		echo '</tr>';
	}
}
echo '</table>';