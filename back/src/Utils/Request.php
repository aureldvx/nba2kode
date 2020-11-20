<?php

namespace App\Utils;

class Request
{
    public static function explodeUri($path)
    {
        $explodedUri = explode('?', $path);
        $paths = null;
        $params = null;
        if (isset($explodedUri[0])) {
            $paths = explode('/', $explodedUri[0]);
            unset($paths[0]);
            $paths = array_values($paths);
        }
        if (isset($explodedUri[1])) {
            $params = [];
            $paramsUri = explode('&', $explodedUri[1]);
            foreach ($paramsUri as $uniqueParam) {
                $explodeParam = explode('=', $uniqueParam);
                if (isset($explodeParam[0]) && isset($explodeParam[1])) {
                    $params[$explodeParam[0]] = $explodeParam[1];
                }
            }
        }

        return [
            'path' => $paths,
            'params' => $params,
        ];
    }
}
