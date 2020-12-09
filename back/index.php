<?php

use App\Controller\AdminController;
use App\Controller\ApiController;
use App\Controller\ImportController;
use App\Controller\LoginController;
use App\Utils\Request;

require './vendor/autoload.php';

// Initialisation des variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('__BASE_DIR__', __DIR__);
define('__TEMPLATES_DIR__', __DIR__ . '/templates/');
define('__APP_SECRET__', $_SERVER['APP_SECRET']);

session_start();

$request = Request::explodeUri($_SERVER['REQUEST_URI']);

// Routing
if ('admin' === $request['path'][0]) {
    if (isset($request['path'][1])) {
        switch ($request['path'][1]) {
            case 'create':
                AdminController::create();
                break;
            case 'edit':
                AdminController::edit();
                break;
            case 'delete':
                AdminController::delete();
                break;
            case 'generate-key':
                ApiController::generateKey();
                break;
            default:
                AdminController::index();
                break;
        }
    } else {
        AdminController::index();
    }
} elseif ('api' === $request['path'][0]) {
    if (isset($request['path'][1])) {
        switch ($request['path'][1]) {
            case 'matches':
                ApiController::getAllMatches();
                break;
            case 'delete-match':
                ApiController::deleteMatch();
                break;
            case 'add-match':
                ApiController::addMatch();
                break;
        }
    }
} else {
    switch ($request['path'][0]) {
        case 'import':
            ImportController::importDataFromApi();
            break;
        case 'first-run':
            ImportController::importDataFromApi();
            LoginController::createModel();
            break;
        case 'signup':
            LoginController::signup();
            break;
        case 'logout':
            LoginController::logout();
            break;
        case 'login':
        default:
            LoginController::login();
        break;
    }
}
