<?php

require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_SESSION['user_id'])) {
	// log out current user, if any
	logout();

	flash('success', 'Se ha desconectado correctamente.');

	// redirect user
	redirect('/index.php');
}