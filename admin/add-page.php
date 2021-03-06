<?php
$titlu_pagina = '';
$continut_pagina = '';
$meniu = '';
$mesaj = '';

/* Primeste detaliile din $_POST daca forma a fost trimisa deja
------------------------------------------------*/
if(!empty($_POST)){
	if( !empty($_POST['titlu_pagina']) ){
		$titlu_pagina = strip_tags( $_POST['titlu_pagina'] );
	}
	if( !empty($_POST['continut_pagina']) ){
		$continut_pagina = $_POST['continut_pagina'];
	}
	if( !empty($_POST['meniu']) ){
		$meniu = $_POST['meniu'];
	}
}

/* Primeste detaliile din DB, daca forma NU a fost trimisa, pentru a popula forma.
------------------------------------------------*/
if( empty($_POST) && !empty($_GET['id']) ){
	$id       = strip_tags($_GET['id']);
	$pagina = primire_pagina_dupa_id( intval( $id ) );

	$titlu_pagina = $pagina['titlu'];
	$continut_pagina = $pagina['continut'];
	$meniu = $pagina['meniu'];
}

/* Adauga pagina noua daca id-ul nu este setat in URL.
------------------------------------------------*/
elseif( !empty($_POST) && empty($_GET['id']) ){
	//daca titlul e setat, adauga pagina
	if( !empty($titlu_pagina) ){
		$mesaj = adauga_pagina( $titlu_pagina, $continut_pagina, $meniu );
	}
	else{
		$mesaj = '<div class="alert alert-warning">Introduceti titlul, va rog.</div>';
	}
}

/* Editeaza o pagina existenta daca forma a fost trimisa si avem id-ul din URL
------------------------------------------------*/
elseif( !empty($_POST) && !empty($_GET['id']) ){
	//daca titlul e setat, adauga pagina
	if( !empty($titlu_pagina) ){
		$mesaj = editeaza_pagina( $titlu_pagina, $continut_pagina, $meniu, strip_tags($_GET['id']) );
	}
	else{
		$mesaj = '<div class="alert alert-warning">Introduceti titlul, va rog.</div>';
	}
}


echo $mesaj;
?>
<form action="" method="post">
	<div class="form-group">
		<label>Titlul paginii</label>
		<?php echo Field::text('titlu_pagina', $titlu_pagina); ?>
	</div>
	<div class="form-group">
		<label>Continutul paginii</label>
		<?php echo Field::textarea('continut_pagina', $continut_pagina, array(
			'rows' => 15,
			'class' => 'admin-textarea',
		)); ?>
	</div>
	<div class="form-group">
		<label>Adauga in meniu</label>
		<?php echo Field::radio('meniu', $meniu, array(
			'no' => 'Nici un meniu',
			'sus' => 'Sus',
			'jos' => 'Jos',
		)); ?>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-primary">Trimite</button>
		<a href="<?php 
			echo add_query_arg(array(
				'section' => 'pages',
			));
		?>" class="btn btn-link">Anuleaza</a>

	</div>
</form>