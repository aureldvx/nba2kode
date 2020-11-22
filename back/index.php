<?php

use App\Controller\AdminController;
use App\Controller\ImportController;
use App\Controller\LoginController;
use App\Utils\Request;
use App\Utils\Services;

require './vendor/autoload.php';

// Initialisation des variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('__BASE_DIR__', __DIR__);
define('__APP_SECRET__', $_SERVER['APP_SECRET']);

session_start();

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
        case 'import':
            Services::dump(ImportController::importTeamStats());
            break;
        default:
            LoginController::login();
            break;
    }
}
