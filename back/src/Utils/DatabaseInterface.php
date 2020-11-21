<?php

namespace App\Utils;

use PDO;
use PDOException;

class DatabaseInterface extends Services
{
    protected ?PDO $pdo = null;

    protected function connect()
    {
        if (!$this->pdo) {
            try {
                $this->pdo = new PDO('mysql:host='.Services::getEnv('DB_HOST').';port='.Services::getEnv('DB_PORT').';dbname='.Services::getEnv('DB_NAME'),
                    Services::getEnv('DB_USER'),
                    Services::getEnv('DB_PASSWORD'),
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
                    ]
                );
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }

        return $this->pdo;
    }

    protected function disconnect()
    {
        if ($this->pdo) {
            $this->pdo = null;
        }
    }
}
