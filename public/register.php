<?php

require_once '../includes/functions.php';

if (array_key_exists('user_id', $_SESSION)) {
    redirect('/dashboard.php');
}

$apellido = $nombre = $email = $password = $password_confirm = '';
$apellido_err = $nombre_err = $email_err = $password_err = $password_confirm_err = '';
$title = 'Registro de usuario';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apellido'], $_POST['nombre'], $_POST['email'], $_POST['password'], $_POST['password_confirm']) )
{
	$post = filter_post();
	
	/*
	 * Validamos el apellido
	 */
	if (empty($post['apellido']))
	{
	    $apellido_err = 'El campo apellido es requerido.';
	}
	else
	{
	    $apellido = $post['apellido'];
	    if(!checkIfOnlyLetters($apellido)) {
	        $apellido_err = 'El campo apellido solo debe contener caracteres alfabéticos.';
	    }
	    elseif(!meetLength($apellido, 3, 20)) {
	        $apellido_err = 'El campo apellido debe incluir entre 3 y 20 letras.';
	    }
	}
	/*
	 * Validamos el nombre
	 */
	if (empty($post['nombre']))
	{
	    $nombre_err = 'El campo nombre es requerido.';
	}
	else
	{
	    $nombre = $post['nombre'];
	    if(!checkIfOnlyLetters($nombre)) {
	        $nombre_err = 'El campo nombre  solo debe contener caracteres alfabéticos.';
	    }
	    elseif(!meetLength($nombre, 3, 20)) {
	        $nombre_err = 'El campo nombre debe incluir entre 3 y 20 letras.';
	    }
	}

	/**
	 * Validamos el email y comprobamos si no existe en la base de datos
	 */
	if (empty($post['email'])) {
	    $email_err = 'El campo correo electrónico es requerido.';
	}
	else
	{
	    $email = $post['email'];
	    if (!checkEmailAddress($email)) {
	        $email_err = "El campo correo electrónico no es válido.";
	    }
	    else
	    {
	        // consultamos la tabla por el email
	        $rows = query('SELECT user_id FROM user WHERE user_email=?', $email);

	        // Si existe el email
	        if( count($rows) != 0 ){
	            $email_err = 'El correo electrónico "'. $email . '" ya está registrado. Por favor Inicia sesión.';
	            //Error: An account is already registered with your email address. Please log in.
	        }
	    }
	}

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
	if (
	    empty( $apellido_err ) &&
	    empty( $nombre_err ) &&
	    empty( $email_err ) &&
	    empty( $password_err ) &&
	    empty( $password_confirm_err )
	){
		// Generamos un código de activación
		$activationkey = bin2hex(openssl_random_pseudo_bytes(16));
		// Creamos el hash de la password
		$password = password_hash($password . 'P4^ncFD!i', PASSWORD_DEFAULT);
		// Creamos la fecha y hora actual
		$dateTime = date('Y-m-d H:i:s');

		$q = 'INSERT INTO user (last_name, first_name, user_email, password, activation, created_on, last_modified_on) VALUES(?,?,?,?,?,?,?);';
		$insert_result = query($q, $apellido, $nombre, $email, $password, $activationkey, $dateTime, $dateTime);

		// Si true => todo salió bien.
		if ($insert_result) {
			
			$to = $email;
			$subject = 'Confirmación de registro';
			$headers = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From:PHPApp <noreply@bwpoffyoucode.com>' . "\r\n";
			$message = "Para activar su cuenta haga clic en el siguiente enlace:\n\n";
			$message .= '<p><a href="'. BASE_URL . "/activate.php?email=" . urldecode($email) . "&key=" . urlencode($activationkey) .'">Confirmación de registro</a></p>';
		    
		    if( mail($to, $subject , $message, $headers) ) {
		        // seteamos el mensage flash para la vista
		        flash('success', 'Gracias por registrarse! Un correo electrónico de confirmación a sido enviado a <b>' . $email . '.</b><br> Por favor, haga clic en el enlace de ese correo electrónico para activar su cuenta.');
		        // re dirigimos al usuario a la página de login
		        redirect('/login.php');
		    }
		}
		
		flash('error', 'El registro no fue realizado.', 'danger');
	}
}

require_once '../views/auth/register_form.phtml';
