<!-- <?php 
$username = '';
$password = '';
$tel = '';
$email = '';
 ?> -->
<div class="row">
	<div class="col-sm-3">
		<div class="service">
			<a href="<?php echo get_page_url('asigurare'); ?>">
				<img src="img/01.png" alt="Asiguare">
			</a>
			<h3>Asigurare</h3>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			tempor incididunt ut labore et dolore magna aliqua.</p>
			<a class="btn btn-primary" href="<?php echo get_page_url('asigurare'); ?>">Detalii</a>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="service">
			<a href="<?php echo get_page_url('hello-mate'); ?>">
				<img src="img/04.png" alt="Locatia">
			</a>
			<h3>Locatia</h3>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			tempor incididunt ut labore et dolore magna aliqua.</p>
			<a class="btn btn-primary" href="<?php echo get_page_url('hello-mate'); ?>">Detalii</a>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="service">
			<a href="<?php echo get_page_url('asigurare'); ?>">
				<img src="img/03.png" alt="Suport">
			</a>				
			<h3>24/7 Suport</h3>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			tempor incididunt ut labore et dolore magna aliqua.</p>
			<a class="btn btn-primary" href="#">Detalii</a>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="service">
			<img src="img/02.png" alt="Despre-noi">
			<h3>Despre noi</h3>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			tempor incididunt ut labore et dolore magna aliqua.</p>
			<a class="btn btn-primary" href="#">Detalii</a>
		</div>
	</div>
</div>

<hr class="hr-styled">

<div class="home-section">
	<div class="section-title">
		<h2>Esti grabit?</h2>
		<div class="subheading">Lasa detaliile aici iar noi te vom contact cit mai curind</div>
	</div>
	<form method="post">

		<div class="row">

			<div class="col-sm-4 form-group">
				<label>Nume:</label>
				<?php echo Field::text('username', $username); ?>
			</div>

			<div class="col-sm-4 form-group">
				<label>Email:</label>
				<?php echo Field::text('email', $email,'email'); ?>
			</div>

			<div class="col-sm-4 form-group">
				<label>Telefon:</label>
				<?php echo Field::number('telefon', $tel,'number'); ?>
			</div>

			<div class="col-sm-12 form-group">
				<button type="submit" class="btn btn-primary pull-right">Trimite</button>
			</div>

		</div>	
		
	</form>
</div>

<hr class="hr-styled">

<div class="home-section">
	<div class="section-title">
		<h2>Asigurare</h2>
		<div class="subheading">Noi avem grija de automobilul dumneavoastra!</div>
	</div>

	<div class="row">
		<div class="col-sm-5">
			<img src="img/insurance.jpg" alt="..." class="img-responsive">
		</div>
		<div class="col-sm-7">
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
			quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
			consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
			cillum dolore eu fugiat nulla pariatur.</p>

			<p> Excepteur sint occaecat cupidatat non
			proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
			Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
			quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
			consequat.</p>

			<p>Excepteur sint occaecat cupidatat non
			proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
			Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			tempor incididunt ut labore et dolore magna aliqua.</p>
		</div>
	</div>
</div>

<hr class="hr-styled">

<div class="home-section">
	<div class="section-title">
		<h2>Beneficii</h2>
		<div class="subheading">Aici sunt citeva motive pentru care trebuie compania noastra e cea mai buna</div>
	</div>

	<table class="table table-striped">
		<tr><td>1. Lorem ipsum dolor sit amet, consectetur adipisicing elit.</td></tr>
		<tr><td>2. Lorem ipsum dolor sit amet, consectetur adipisicing elit.</td></tr>
		<tr><td>3. Lorem ipsum dolor sit amet, consectetur adipisicing elit.</td></tr>
		<tr><td>4. Lorem ipsum dolor sit amet, consectetur adipisicing elit.</td></tr>
		<tr><td>5. Lorem ipsum dolor sit amet, consectetur adipisicing elit.</td></tr>
		<tr><td>6. Lorem ipsum dolor sit amet, consectetur adipisicing elit.</td></tr>
		<tr><td>7. Lorem ipsum dolor sit amet, consectetur adipisicing elit.</td></tr>
		<tr><td>8. Lorem ipsum dolor sit amet, consectetur adipisicing elit.</td></tr>
		<tr><td>9. Lorem ipsum dolor sit amet, consectetur adipisicing elit.</td></tr>
		<tr><td>10. Lorem ipsum dolor sit amet, consectetur adipisicing elit.</td></tr>
	</table>
</div>