<?php

namespace App\Utils;

class Services
{
    protected static function getEnv(string $param)
    {
        if (!isset($_SERVER[$param])) {
            return false;
        }

        return $_SERVER[$param];
    }
}
