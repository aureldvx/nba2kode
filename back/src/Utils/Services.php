<?php

namespace App\Utils;

use Exception;

class Services
{
    private static array $flashTypes = ['error', 'success'];


    /**
     * Get specified environment variable.
     *
     * @param string $param
     *
     * @return Exception|mixed
     */
    protected static function getEnv(string $param)
    {
        if (!isset($_SERVER[$param])) {
            return new Exception("La variable demandée $param n'a pas été définie.");
        }

        return $_SERVER[$param];
    }


    /**
     * Good styling dump.
     *
     * @param $var
     */
    public static function dump($var)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }


    /**
     * Create new flash notification.
     *
     * @param string $type
     * @param string $message
     *
     * @return void|Exception
     */
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


    /**
     * Sanitize a user input.
     *
     * @param      $entry
     * @param      $sanitizer
     * @param null $validator
     *
     * @return mixed
     */
    public static function sanitizeEntry($entry, $sanitizer, $validator = null)
    {
        $filtered = filter_var($entry, $sanitizer);
        if ($validator) {
            $filtered = filter_var($filtered, $validator);
        }
        return $filtered;
    }


    /**
     * Hash a given string.
     *
     * @param string $password
     *
     * @return false|string|null
     */
    public static function hashPassword(string $password)
    {
        return password_hash($password, PASSWORD_ARGON2I);
    }
}
