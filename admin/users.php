<?php
/* Sterge o pagina
------------------------------------------------*/
if( !empty($_GET['action']) && $_GET['action'] == 'delete' ){
	delete_user();
}

/* Afiseaza toate paginile
------------------------------------------------*/
$users = get_users();
echo '<table class="table table-striped">';
echo '<tr>
	<th>ID</th>
	<th>Username</th>
	<th>Email</th>
	<th>Tip</th>
	<th>Opțiuni</th>
</tr>';

if( is_array($users) ){
	foreach ($users as $user) {
		echo '<tr>';
		echo '<td>'. $user['id'] .'</td>';
		echo '<td>'. $user['username'] .'</td>';
		echo '<td>'. $user['email'] .'</td>';
		echo '<td>'. $user['type'] .'</td>';
		
		echo '<td>';
		
		if( 'superadmin' !== $user['type'] ){
			echo '<a class="btn btn-primary btn-xs" href="'. add_query_arg(array(
				'section' => 'add-user',
				'id' => $user['id'],
			)) .'" title="Editeaza"><span class="glyphicon glyphicon-pencil"></span></a>
			<a class="btn btn-danger btn-xs" href="'. add_query_arg( array( 
				'action' => 'delete',
				'id' => $user['id'],
			) ) .'" data-confirm-delete="Sunteti sigur ca doriti sa eliminati acest utilizator: '. $user['username'] .' ?" title="Sterge"><span class="glyphicon glyphicon-trash"></span></a>';
		}
		
		echo '</td>';

		echo '</tr>';
	}
}
echo '</table>';