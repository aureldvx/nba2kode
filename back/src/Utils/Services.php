<?php

namespace App\Utils;

use Exception;

class Services
{
    private static array $flashTypes = ['error', 'success'];

    protected static function getEnv(string $param)
    {
        if (!isset($_SERVER[$param])) {
            return false;
        }

        return $_SERVER[$param];
    }

    public static function dump($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }

    public static function addFlash(string $type, string $message)
    {
        if (!isset($_SESSION['flashes'])) {
            $_SESSION['flashes'] = [];
        }

        if (!in_array($type, self::$flashTypes)) {
            return new Exception("Le type spécifié pour la notification n'est pas autorisé.");
        }

        $_SESSION['flashes'][] = [
            'type' => $type,
            'message' => $message
        ];
    }
}
