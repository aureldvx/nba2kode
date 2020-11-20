<?php

namespace App\Controller;

use App\Utils\DatabaseInterface;

class LoginController extends AbstractController
{
    public static function login()
    {
        var_dump(DatabaseInterface::test());
        // return AbstractController::renderView('home', array(
        //     'title' => 'ok c cool'
        // ));
    }
}
