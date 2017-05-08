<?php 
require_once dirname(__FILE__) . "/functions.php";

session_start();

// Delogare redirect
if( !empty($_GET['action']) && $_GET['action'] == 'delogare' ){
	header('Location: admin.php');
}

// Logare
$mesaj = '';
$conn = conectare_la_db();
$form_redirect = false;

if( !empty($_POST['username']) ){
	$username = strip_tags($_POST['username']);
}
else{
	$username = '';
}
if( !empty($_POST['password']) ){
	$password = strip_tags($_POST['password']);
}
else{
	$password = '';
}

if( !empty($_POST) && !empty($_POST['username']) && !empty($_POST['password']) ){
	$username = strip_tags($_POST['username']);
	$password = strip_tags($_POST['password']);

	$sql = "SELECT * FROM users WHERE username = '$username'";
	$result = mysqli_query($conn, $sql);

	// Mysql_num_row is counting table row
	$count = mysqli_num_rows($result);
	// If result matched $username and $password, table row must be 1 row
	if( $count == 1 ){
	    $row = mysqli_fetch_assoc($result);
	    if ( md5($password) == $row['password']){
	        $_SESSION['username'] = $username;
	        $_SESSION['password'] = $password;
	        $mesaj = "<div class=\"alert alert-success\">Login Successful</div>";
	        $form_redirect = true;
	    }
	    else {
	        $mesaj = '<div class="alert alert-warning">Parola sau Numele de utilizator e gresit!</div>';
	    }
	}
	else{
	    $mesaj = '<div class="alert alert-warning">Parola sau Numele de utilizator e gresit!</div>';
	}
}
elseif( !empty($_POST) && ( empty($_POST['username']) || empty($_POST['password']) )  ){
	$mesaj = '<div class="alert alert-warning">Introduceti detaliile pentru logare.</div>';
}

// Logare redirect
if( !empty($form_redirect) ){
	header('Location: admin.php');
}

get_header( 'admin' );

if( !empty($_SESSION['username']) && !empty($_SESSION['password']) ){
?>
<div class="row">

	<ul class="nav nav-tabs">
		<?php 
			
			$meniu = array(
				'admin' => 'Admin',
				'adauga-pagina' => 'Adauga pagina',
				'pagini' => 'Pagini',
				'cereri' => 'Cereri',
				'delogare' => 'Delogare',
			);
			
			foreach ($meniu as $id => $label) {
				
				// Formeaza URL pentru fiecare tab in dependenta de ID
				if( 'delogare' == $id ){
					$link = '?action='. $id;
				}
				elseif( 'admin' == $id ){
					$link = 'admin.php';
				}
				else{
					$link = '?section=' .$id;
				}

				// Marcheaza tab-ul activ
				$active = !empty($_GET['section']) && $_GET['section'] == $id ? ' active' : '';

				// Daca suntem pe pagina pricipala din admin, atunci primul tab este active, implicit.
				if( empty( $_GET['section'] ) && 'admin' == $id ){
					$active = ' active';
				}

				// Afiseaza linkul
				echo '<li class="admin-nav-'. $id . $active .'"><a href="'. $link .'">'. $label .'</a></li>';
			}
		?>
	</ul>

	<div class="admin-section">
		<?php
			if( !empty($_GET['section']) ){
				if( $_GET['section'] == 'adauga-pagina' ){
					include dirname(__FILE__) . "/admin/adauga-pagina.php";
				}
				if( $_GET['section'] == 'pagini' ){
					include dirname(__FILE__) . "/admin/admin-pagini.php";
				}
				if( $_GET['section'] == 'cereri' ){
					include dirname(__FILE__) . "/admin/cereri.php";
				}
			}
			else{
				echo '<div class="alert alert-info">Selectati o actiune din meniu!</div>';
			}
		?>
	</div>

</div> <!-- .row -->
<?php
}
else{
	include dirname(__FILE__) . "/login.php";
}

if( !empty($_GET['action']) && $_GET['action'] == 'delogare' ){
	session_destroy();
}

get_footer();