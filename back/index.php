<?php

use App\Controller\AdminController;
use App\Controller\LoginController;
use App\Utils\Request;

require './vendor/autoload.php';

// Initialisation des variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('__BASE_DIR__', __DIR__);
define('__APP_SECRET__', $_SERVER['APP_SECRET']);

$request = Request::explodeUri($_SERVER['REQUEST_URI']);

// Routing
if ('admin' === $request['path'][0]) {
    switch ($request['path'][1]) {
        case '':

            break;
        default:
            AdminController::index();
            break;
    }
} else {
    switch ($request['path'][0]) {
        default:
            LoginController::login();
            break;
    }
}
