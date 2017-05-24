<?php 
require_once dirname(__FILE__) . "/functions.php";

$admin_sections = array(
	'cereri' => array(
		'label' => 'Cereri',
		'file' => dirname(__FILE__) .'/admin/cereri.php',
		'icon' => 'img/icons/id-card.png',
		'in_tab' => true,
		'button' => 'add-cerere',
		'access' => 'moderator',
	),
	'add-cerere' => array(
		'label' => 'Adauga cerere',
		'file' => dirname(__FILE__) .'/admin/add-cerere.php',
		'icon' => 'img/icons/add-cerere.png',
		'access' => 'moderator',
	),
	'pages' => array(
		'label' => 'Pagini',
		'file' => dirname(__FILE__) .'/admin/pages.php',
		'icon' => 'img/icons/pages.png',
		'in_tab' => true,
		'button' => 'add-page',
		'access' => 'admin',
	),
	'add-page' => array(
		'label' => 'Adauga pagina',
		'file' => dirname(__FILE__) .'/admin/add-page.php',
		'icon' => 'img/icons/add-page.png',
		'access' => 'admin',
	),
	'users' => array(
		'label' => 'Utilizatori',
		'file' => dirname(__FILE__) .'/admin/users.php',
		'icon' => 'img/icons/users.png',
		'in_tab' => true,
		'button' => 'add-user',
		'access' => 'admin',
	),
	'add-user' => array(
		'label' => 'Adauga utilizator',
		'file' => dirname(__FILE__) .'/admin/add-user.php',
		'icon' => 'img/icons/add-user.png',
		'access' => 'admin',
	),
	'mail-settings' => array(
		'label' => 'Setări mail',
		'file' => dirname(__FILE__) .'/admin/mail-settings.php',
		'icon' => 'img/icons/email.png',
		'access' => 'admin',
	),
	'mail-templates' => array(
		'label' => 'Șabloane mail',
		'file' => dirname(__FILE__) .'/admin/mail-templates.php',
		'icon' => 'img/icons/email-templates.png',
		'access' => 'admin',
	),
	'settings' => array(
		'label' => 'Setări',
		'file' => dirname(__FILE__) .'/admin/settings.php',
		'icon' => 'img/icons/settings.png',
		'access' => 'admin',
	),
);


session_start();

// Delogare redirect
if( !empty($_GET['action']) && $_GET['action'] == 'delogare' ){
	header('Location: admin.php');
}

// Logare
$mesaj = '';
$conn = conectare_la_db();
$form_redirect = false;

if( !empty($_POST['authentication_form']) ){
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

	if( !empty($username) && !empty($password) ){

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
}
// Logare redirect
if( !empty($form_redirect) ){
	header('Location: admin.php');
}

// Template start

get_header( 'admin' );

if( !empty($_SESSION['username']) && !empty($_SESSION['password']) ){
?>
<div class="row">

	<ul class="nav nav-tabs">
		<?php 
			$main_active = empty($_GET['section']) || ( ! empty($_GET['section']) && empty( $admin_sections[ $_GET['section'] ] ) ) ? ' active' : '';
			echo '<li class="admin-nav-admin'. $main_active .'"><a href="?section=admin">Admin</a></li>';

			foreach ($admin_sections as $section_id => $section) {
				
				if( ! current_user_is( $section['access'] ) )
					continue;

				// Formeaza URL pentru fiecare tab in dependenta de ID daca nu este setat in array
				$link = !empty($section['url']) ? $section['url'] : '?section='. $section_id;
				
				// Marcheaza tab-ul activ
				$active = !empty($_GET['section']) && $_GET['section'] == $section_id ? ' active' : '';

				// Afiseaza linkul
				if( !empty($section['in_tab']) ){
					echo '<li class="admin-nav-'. $section_id . $active .'"><a href="'. $link .'">'. $section['label'] .'</a></li>';
				}
			}
			
			echo '<li class="admin-nav-delogare"><a href="?action=delogare">Delogare</a></li>';
		?>
	</ul>

	<div class="admin-section">
		<?php

			//We are on a specific admin page
			if( 
				!empty($_GET['section']) && 
				'admin' !== $_GET['section']  &&
				!empty($admin_sections[ $_GET['section'] ]['file']) &&
				file_exists($admin_sections[ $_GET['section'] ]['file'])
			){

				if( !empty($_GET['section']) && !empty($admin_sections[ $_GET['section'] ]) ){
					echo '<h4 class="admin-page-title">'. $admin_sections[ $_GET['section'] ]['label'];
						
						if( !empty($admin_sections[ $_GET['section'] ]['button']) ){
							$btn_id = $admin_sections[ $_GET['section'] ]['button'];
							$btn_section = $admin_sections[ $btn_id ];
							$btn_link = !empty($btn_section['url']) ? $btn_section['url'] : '?section='. $btn_id;
							echo '<a href="'. $btn_link .'" class="label bg-primary">'. $btn_section['label'] .'</a>';
						}

					echo '</h4>';
				}

				if( current_user_is( $admin_sections[ $_GET['section'] ]['access'] ) ){
					include $admin_sections[ $_GET['section'] ]['file'];
				}
			}

			// We are on main admin page
			else{
				echo '<div class="admin-points">';
					foreach ($admin_sections as $section_id => $section) {
						
						if( ! current_user_is( $section['access'] ) )
							continue;

						// Formeaza URL pentru fiecare tab in dependenta de ID daca nu este setat in array
						$link = !empty($section['url']) ? $section['url'] : '?section='. $section_id;

						$icon = !empty($section['icon']) ? $section['icon'] : 'img/icons/file.png';

						echo '<a href="'. $link .'" class="col-xs-6 col-sm-3">';
							echo '<div class="point">';
								echo '<div class="point-icon"><img src="'. $icon .'" alt="" /></div>';
								echo '<div class="point-label">'. $section['label'] .'</div>';
							echo '</div>';
						echo '</a>';

					}
				echo '</div>';
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