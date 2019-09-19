<?php

require_once '../includes/functions.php';

if (array_key_exists('user_id', $_SESSION)) {
    redirect('/dashboard.php');
}

$email = $password = '';
$email_err = $password_err = '';
$title = 'Iniciar sesión';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'], $_POST['password']) ) {

	$post = filter_post();
	/**
	 * Comprobamos que el email no este vacío
	 */
	if (!empty($post['email'])) {
	    $email = $post["email"];
	} else {
	    $email_err = 'El campo correo electrónico es requerido.';
	}

	/**
	 * Comprobamos que la password no este vacía
	 */
	if (!empty($post['password'])) {
	    $password = $post["password"];
	} else {
	    $password_err = 'El campo contraseña es requerido.';
	}

	/**
	 * Si todo esta ok
	 */
	if (empty($email_err) && empty($password_err)) {
	    // 
		$rows = query("SELECT * FROM user WHERE user_email=? AND activation='activated' AND deleted=0", $email);
		// Si encontramos al usuario
		if (count($rows) == 1) {
			$user = $rows[0];
			if (password_verify($password . 'P4^ncFD!i', $user['password'] ) == $user['password']) {
				// remember that user's now logged in by storing user's ID in session
				$_SESSION["user_id"] = $user["user_id"];
				$_SESSION["user"] = $user["first_name"];
				// redirect to portfolio
	            redirect("/dashboard.php");
			}
		}

		// flash('error', 'El usuario o la contraseña ingresada es incorrecta.', 'danger');
	    flash('error', 'El usuario o la contraseña ingresada es incorrecta o aún no ha activado su cuenta.', 'primary');
	}
}


require_once '../views/auth/login_form.phtml';
