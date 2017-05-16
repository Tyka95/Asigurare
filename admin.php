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

if( !empty($_POST) && !empty($username) && !empty($password) ){

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
elseif( !empty($_POST) && ( empty($username) || empty($password) ) ){
	$mesaj = '<div class="alert alert-warning">Introduceti detaliile pentru logare.</div>';
}

// Logare redirect
if( !empty($form_redirect) ){
	header('Location: admin.php');
}

get_header( 'admin' );


$admin_sections = array(
	'admin' => array(
		'label' => 'Admin',
		'url' => 'admin.php',
	),
	'pages' => array(
		'label' => 'Pagini',
		'file' => dirname(__FILE__) .'/admin/pages.php',
	),
	'cereri' => array(
		'label' => 'Cereri',
		'file' => dirname(__FILE__) .'/admin/cereri.php',
	),
	'users' => array(
		'label' => 'Utilizatori',
		'file' => dirname(__FILE__) .'/admin/users.php',
	),
	'add-page' => array(
		'label' => 'Adauga pagina',
		'file' => dirname(__FILE__) .'/admin/add-page.php',
	),
	'add-user' => array(
		'label' => 'Adauga utilizator',
		'file' => dirname(__FILE__) .'/admin/add-user.php',
	),
	'delogare' => array(
		'label' => 'Delogare',
		'url' => '?action=delogare',
	),
);


if( !empty($_SESSION['username']) && !empty($_SESSION['password']) ){
?>
<div class="row">

	<ul class="nav nav-tabs">
		<?php 
			foreach ($admin_sections as $section_id => $section) {
				
				// Formeaza URL pentru fiecare tab in dependenta de ID daca nu este setat in array
				$link = !empty($section['url']) ? $section['url'] : '?section='. $section_id;

				// Marcheaza tab-ul activ
				$active = !empty($_GET['section']) && $_GET['section'] == $section_id ? ' active' : '';

				// Daca suntem pe pagina pricipala din admin, atunci primul tab este active, implicit.
				if( empty( $_GET['section'] ) && 'admin' == $section_id ){
					$active = ' active';
				}

				// Afiseaza linkul
				echo '<li class="admin-nav-'. $section_id . $active .'"><a href="'. $link .'">'. $section['label'] .'</a></li>';
			}
		?>
	</ul>

	<div class="admin-section">
		<?php
			if( 
				!empty($_GET['section']) && 
				'admin' !== $_GET['section']  &&
				!empty($admin_sections[ $_GET['section'] ]['file']) &&
				file_exists($admin_sections[ $_GET['section'] ]['file'])
			){
				include $admin_sections[ $_GET['section'] ]['file'];
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