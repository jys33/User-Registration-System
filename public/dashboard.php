<?php

require_once '../includes/functions.php';

$title = 'Dashboard';

if (!array_key_exists('user_id', $_SESSION)) {
    redirect('/index.php');
}

require_once '../views/pages/dashboard.phtml';