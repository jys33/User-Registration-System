<?php

require_once '../includes/functions.php';

$title = 'Dashboard';

if (!array_key_exists('id', $_SESSION)) {
    redirect('/index.php');
}

require_once '../views/pages/dashboard.phtml';