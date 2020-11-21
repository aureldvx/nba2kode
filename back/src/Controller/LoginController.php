<?php

namespace App\Controller;

use App\Utils\QueryBuilder;
use App\Utils\Services;
use PDO;

class LoginController extends AbstractController
{
    public static function login()
    {
        $query = new QueryBuilder();
        $results = $query
            ->insert(['name' => ':name'])
            ->inTable('usrs')
            ->setParameters([
                [':name', 'slgrgrtmec', PDO::PARAM_STR],
            ])
            ->getQuery()
            ->getResult();

        Services::dump($results);

//        return AbstractController::renderView('home', [
//            'title' => 'ok c cool',
//        ]);
    }
}
