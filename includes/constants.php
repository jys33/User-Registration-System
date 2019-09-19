<?php

// your database's server
define("SERVER", "localhost");

// your database's name
define("DATABASE", "bwp");
// define("DATABASE", "bwpoffyo_app");

// your database's username
define("USERNAME", "root");
// define("USERNAME", "bwpoffyo_user");

// your database's password
define("PASSWORD", "");
// define("PASSWORD", "R$=3ALdZjEHZ");

// Site URL (base for all redirections):
define('BASE_URL', 'http://bwp.offyoucode.co.uk');

// App root
define('APP_ROOT', dirname(dirname(__FILE__)));

// App versión
define('APP_VERSION', '1.0.0');

// Site Name
define('SITE_NAME', 'App');

/* Incluimos la función en esté archivo ya que esta presente en todos los demás*/
session_start();

/* Establece la zona horaria predeterminada*/
date_default_timezone_set('America/Argentina/Buenos_Aires');

