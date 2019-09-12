<?php

require_once '../includes/functions.php';

if (array_key_exists('user_id', $_SESSION)) {
    redirect('/dashboard.php');
}

$email = '';
$email_err = '';

$title = '¿Olvidaste tu contraseña?';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) ) {

	$post = filter_post();
	/**
	 * Comprobamos que el email no este vacío
	 */
	if (!empty($post['email'])) {
	    $email = $post["email"];
	    if (!checkEmailAddress($email)) {
	        $email_err = "El correo electrónico no es válido.";
	    }
	    else
	    {
	        // consultamos la tabla por el email
	        $rows = query('SELECT user_id FROM user WHERE user_email=?', $email);

	        // Si no existe el email
	        if( count($rows) == 0 ){
	            $email_err = 'Lo sentimos, no encontramos ese correo electrónico.';
	        }
	    }
	} else {
	    $email_err = 'El campo correo electrónico es requerido.';
	}

	/**
	 * Si todo esta ok
	 */
	if (empty($email_err)) {
		$user = $rows[0];
		$user_id = $user['user_id'];

		//Create a unique activation code 32 caracteres
		//Ejemplo KEY: cc58481ee70ce002 7209abf27af17199
		$key = bin2hex(openssl_random_pseudo_bytes(16));
		$time = time();
		$dateTime = date("Y-m-d H:i:s");
		$status = 'pending';
		
		$q = 'INSERT INTO forgot_password (user_id, reset_key, time, status, created_on, last_modified_on) VALUES (?,?,?,?,?,?)';

		$insert_success = query($q, $user_id, $key, $time, $status, $dateTime, $dateTime);

		if ($insert_success) {
		    $to = $email;
		    $subject = 'Restablecimiento de contraseña';
		    $headers = 'MIME-Version: 1.0' . "\r\n";
		    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		    $headers .= 'From:PHPApp <noreply@eduoffyoucode.com>' . "\r\n";
		    $message = "Para restablecer su contraseña haga click en el siguiente enlace:\n\n";
		    $message .= '<p><a href="'. BASE_URL . "/password_reset.php?user_id=" . urlencode($user_id) . "&key=" . urldecode($key) .'">Restablecer contraseña</a></p><p>El enlace expirará en 24 horas.</p>';

		    if( mail($to, $subject , $message, $headers) ) {
		        $msg = 'Se ha enviado un correo electrónico de restablecimiento de contraseña a la dirección de correo electrónico registrada en su cuenta, pero puede tardar varios minutos en aparecer en su bandeja de entrada. Espere al menos 10 minutos antes de intentar otro reinicio.';
		        $success = 'Correo electrónico de restablecimiento de contraseña enviado.';
		    }
		}
	}
}

require_once '../views/auth/forgot_password_form.phtml';
