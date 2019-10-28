<?php

require_once '../includes/functions.php';

if (array_key_exists('id', $_SESSION)) {
    redirect('/dashboard.php');
}

$password = $password_confirm = '';
$password_err = $password_confirm_err = '';

$title = 'Restablecer contraseña';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && 
	isset($_POST['user_id'], $_POST['key'], $_POST['password'], $_POST['password_confirm']) && 
	filter_var($_POST['user_id'], FILTER_VALIDATE_INT) && 
	(strlen($_POST['key']) == 32 ) )
{
	$post = filter_post();

	$time = time() - 86400;

	$user_id = $post['user_id'];
	$key = $post['key'];

	//Run Query: Check combination of user_id & key exists and less than 24h old
	$q = "SELECT user_id FROM forgot_password WHERE reset_key=? AND user_id=? AND time > ? AND status='pending'";
	$rows = query($q, $key, $user_id, $time);

	/**
	 * Si encontramos al usuario en la base de datos
	 */
	if (count($rows) == 1) {
		/*
		 * Validamos la password
		 */
		if (empty($post['password']))
		{
		    $password_err = 'El campo contraseña es requerido.';
		}
		else
		{
		    $password = $post['password'];
		    if(!validatePasswordStrength($password)) {
		        $password_err = 'Elige una contraseña más segura. Prueba con una combinación de letras, números y símbolos.';
		    }
		    else
		    {
		        if(!empty($post["password_confirm"]))
		        {
		            // Comprobamos si las passwords son iguales
		            if($password !== $post['password_confirm']) {
		                $password_confirm_err = 'El campo confirmar contraseña no coincide con el campo contraseña.';
		            }
		        }
		        else
		        {
		            $password_confirm_err = 'El campo confirmar contraseña es requerido.';
		        }
		    }
		}

		// Si todo esta OKAY
		if (empty( $password_err ) && empty( $confirm_password_err )) {
			$dateTime = date("Y-m-d H:i:s");
			$password = password_hash($password . 'P4^ncFD!i', PASSWORD_DEFAULT);
			$q = 'UPDATE user SET password=?, last_modified_on=? WHERE user_id=? LIMIT 1';

			$update_success = query($q, $password, $dateTime, $user_id);

			if ($update_success != 0) {
				$q = "UPDATE forgot_password SET status='used', last_modified_on=? WHERE reset_key=? AND user_id=? LIMIT 1";
				$update_success = query($q, $dateTime, $key, $user_id);
				/**
				 * Si se actualizó el status de la tabla forgot_password
				 */
				if ($update_success != 0) {
					// seteamos el mensage flash para la vista
					flash('success', 'Su contraseña ha sido actualizada correctamente.');
					// re dirigimos al usuario a la página de login
					redirect('/login.php');
				}
			}
	        //echo 'Verificar esté punto';
		}

		require_once '../views/auth/password_reset_form.phtml';
		exit;
	}

}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['user_id'], $_GET['key']) && 
	filter_var($_GET['user_id'], FILTER_VALIDATE_INT) && 
	(strlen($_GET['key']) == 32 ))
{
	// Sanitize GET data
	$get = filter_get();
	$time = time() - 86400;

	$user_id = $get['user_id'];
	$key = $get['key'];

	//Run Query: Check combination of user_id & key exists and less than 24h old
	$q = "SELECT user_id FROM forgot_password WHERE reset_key=? AND user_id=? AND time > ? AND status='pending'";
	$rows = query($q, $key, $user_id, $time);

	/**
	 * Si encontramos al usuario, mostramos el formulario de cambio de password
	 */
	if (count($rows) == 1) {

		require_once '../views/auth/password_reset_form.phtml';
		exit;
	}
}

redirect('/index.php');
