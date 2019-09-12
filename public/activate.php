<?php

require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && 
	isset($_GET['email'], $_GET['key']) && 
	filter_var($_GET['email'], FILTER_VALIDATE_EMAIL) && 
	(strlen($_GET['key']) == 32 ) ) {

	$get = filter_get();

	$email = $get['email'];
	$activationkey = $get['key'];

	//UPDATE user SET activation='activated' WHERE (user_email='johndoe@gmail.com' AND activation='d1584ce5b1fbcd0dbaea0e8a558ee1d7') LIMIT 1
	$activate_success = query("UPDATE user SET activation='activated' WHERE (user_email=? AND activation=?) LIMIT 1", $email, $activationkey);

	if ( $activate_success != 0 ) {
		flash('success', 'Su cuenta ha sido activada, ahora puedes iniciar sesión.');
		// re dirigimos al usuario a la página de login
		redirect('/login.php');
	}
}

redirect('/index.php');