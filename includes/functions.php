<?php

require 'constants.php';

/**
 * funciones de validación
 */
function validatePasswordStrength($password) {
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        return false;
    }else{
        return true;
    }
}

function checkIfOnlyLetters($field) {
    if( !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚÑñÜü ]+$/", $field) ) return false;
    return true;
}

function meetLength($field, $minLength, $maxLength) {
    $strLen = strlen(trim($field));
    if ($strLen >= $minLength && $strLen <= $maxLength) {
        return true;
    } elseif ($strLen < $minLength) {
        return false;
    } else {
        return false;
    }
}

function checkEmailAddress($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) ? false : true;
}

function filter_post() {
	$post = filter_input_array(INPUT_POST);
	$post = array_map('trim', $post);
	$post = array_map('htmlspecialchars', $post);
	$post = array_map('stripslashes', $post);
	return $post;
}

function filter_get() {
    $get = filter_input_array(INPUT_GET);
    $get = array_map('trim', $get);
    $get = array_map('htmlspecialchars', $get);
    $get = array_map('stripslashes', $get);
    return $get;
}

/**
 * Logs out current user, if any.  Based on Example #1 at
 * http://us.php.net/manual/en/function.session-destroy.php.
 */
function logout()
{
    // unset any session variables
    $_SESSION = array();

    // expire cookie
    if (!empty($_COOKIE[session_name()]))
    {
        setcookie(session_name(), "", time() - 42000);
    }

    // destroy session
    session_destroy();
}

/**
 * Redirects user to destination, which can be
 * a URL or a relative path on the local host.
 *
 * Because this function outputs an HTTP header, it
 * must be called before caller outputs any HTML.
 */
function redirect($destination)
{
    // handle URL - manejar la URL
    if (preg_match("/^https?:\/\//", $destination))
    {
        header("Location: " . $destination);
    }

    // handle absolute path - Ruta absoluta
    else if (preg_match("/^\//", $destination)) //la barra \ es el caracter de escape de / en la regEx
    {
        $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"];
        header("Location: $protocol://$host$destination");
        // redirect('/login.php'); => http://localhost/login.php
    }

    // handle relative path - Ruta relativa
    else
    {
        // adapted from http://www.php.net/header
        $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"];
        $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
        header("Location: $protocol://$host$path/$destination");
        // redirect('login.php'); => http://localhost/phpapp/public  login.php
    }

    // exit immediately since we're redirecting anyway
    exit;
}

/**
 * Conexión y consulta a base de datos
 * @return un array de datos, true o false
 */
function query(/* $sql [, ... ] */) {
    // SQL statement
    $sql = func_get_arg(0);

    // parameters, if any
    $parameters = array_slice(func_get_args(), 1);

    static $conn;

    if (!isset($conn)) {
        try {
            //'dsn' => 'mysql:host=localhost;dbname=app'
            $conn = new PDO(
                'mysql:host=' . SERVER . ';dbname=' . DATABASE,
                USERNAME,
                PASSWORD,
                array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                )
            );
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            exit;
        }
    }

    $pattern = "
    /(?:'[^'\\\\]*(?:(?:\\\\.|'')[^'\\\\]*)*'
    | \"[^\"\\\\]*(?:(?:\\\\.|\"\")[^\"\\\\]*)*\"
    | `[^`\\\\]*(?:(?:\\\\.|``)[^`\\\\]*)*`
    )(*SKIP)(*F)| \?/x";

    preg_match_all($pattern, $sql, $matches);
    if (count($matches[0]) < count($parameters)) {
        trigger_error("Too few placeholders in query", E_USER_ERROR);
    } else if (count($matches[0]) > count($parameters)) {
        trigger_error("Too many placeholders in query", E_USER_ERROR);
    }

    // replace placeholders with quoted, escaped strings
    $patterns = [];
    $replacements = [];
    $M = count($parameters);
    for ($i = 0, $n = $M; $i < $n; $i++) {
        array_push($patterns, $pattern);
        array_push($replacements, preg_quote($conn->quote($parameters[$i])));
    }
    $query = preg_replace($patterns, $replacements, $sql, 1);

    // execute query
    $statement = $conn->query($query);
    if ($statement === false) {
        trigger_error($conn->errorInfo()[2], E_USER_ERROR);
    }

    // if query was SELECT
    // http://stackoverflow.com/a/19794473/5156190
    if ($statement->columnCount() > 0) {
        // return result set's rows
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    // if query was DELETE, INSERT, or UPDATE
    else {
        // return number of rows affected
        return ($statement->rowCount() == 1); // true o false
    }
}

/**
 * Show flash messages.
 */
function flash($name = '', $message = '', $class = 'success')
{
    if(!empty($name)){

        if(!empty($message) && empty($_SESSION[$name])){
            if(!empty($_SESSION[$name])){
                unset($_SESSION[$name]);
            }

            if(!empty($_SESSION[$name . '_class'])){
                unset($_SESSION[$name . '_class']);
            }

            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;

        } elseif(empty($message) && !empty($_SESSION[$name])){
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="alert alert-' . $class . ' alert-dismissible fade show" role="alert">' . $_SESSION[$name] . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            //echo '<div class="alert alert-primary center">' . $_SESSION[$name] . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}