<?php

namespace App\Controller;

use App\Utils\QueryBuilder;
use App\Utils\Services;
use DateTime;
use Exception;
use PDO;

class LoginController extends AbstractController
{
    public static function login()
    {
        echo 'login page';

//        return AbstractController::renderView('home', [
//            'title' => 'ok c cool',
//        ]);
    }


    /**
     * Create database models.
     *
     * @return Exception|int
     */
    public static function createModel()
    {
        try {
            (new QueryBuilder())
                ->raw('DROP TABLE IF EXISTS usrs;')
                ->getQuery()
                ->getResult()
            ;

            (new QueryBuilder())
                ->raw('CREATE TABLE IF NOT EXISTS usrs (
                    id INTEGER PRIMARY KEY AUTO_INCREMENT,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    email VARCHAR(200) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    reset_password_token VARCHAR(255),
                    created_at DATETIME NOT NULL,
                    updated_at DATETIME NOT NULL
                )')
                ->getQuery()
                ->getResult()
            ;

            return 1;
        } catch (Exception $exception) {
            return new Exception("Une erreur s'est produit lors de la création du Model Utilisateur : $exception");
        }
    }


    public static function signup()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = Services::sanitizeEntry($_POST['username'], FILTER_SANITIZE_STRING);
            $email = Services::sanitizeEntry($_POST['email'], FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL);
            $password = Services::sanitizeEntry($_POST['password'], FILTER_UNSAFE_RAW);
            $repeatedPassword = Services::sanitizeEntry($_POST['confirm_password'], FILTER_UNSAFE_RAW);
            $token = Services::sanitizeEntry($_POST['_csrf_token'], FILTER_UNSAFE_RAW);

            $errors = false;
            if ($password === '' || $repeatedPassword === '') {
                Services::addFlash('error', 'Merci de renseigner un mot de passe pour valider l\'inscription');
                $errors = true;
            }

            if ($password !== $repeatedPassword) {
                Services::addFlash('error', 'Les deux mots de passe renseignés ne correspondent pas.');
                $errors = true;
            }

            if (!$email) {
                Services::addFlash('error', 'Merci de rentrer un email valide pour valider l\'inscription.');
                $errors = true;
            }

            if ($username === '') {
                Services::addFlash('error', 'Merci de rentrer un nom d\'utilisateur valide pour valider l\'inscription.');
                $errors = true;
            }

            Services::dump($token);
            Services::dump($_SESSION['_csrf_token']);
            if ($token !== $_SESSION['_csrf_token']) {
                Services::addFlash('error', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer');
                $errors = true;
            }

            if (!$errors) {
                (new QueryBuilder())
                    ->insert([
                        'username' => ':username',
                        'email' => ':email',
                        'password' => ':password',
                        'created_at' => ':created_at',
                        'updated_at' => ':updated_at'
                    ])
                    ->inTable('usrs')
                    ->setParameters(
                        [
                            [':username', $username, PDO::PARAM_STR],
                            [':email', $email, PDO::PARAM_STR],
                            [':password', Services::hashPassword($password), PDO::PARAM_STR],
                            [':created_at', (new DateTime('now', new \DateTimeZone('Europe/Paris')))->format('Y-m-d H:i:s'), PDO::PARAM_STR],
                            [':updated_at', (new DateTime('now', new \DateTimeZone('Europe/Paris')))->format('Y-m-d H:i:s'), PDO::PARAM_STR],
                        ]
                    )
                    ->getQuery()
                    ->getResult();

                return 'Inscription finalisée';
            }
        }

        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));

        return AbstractController::renderView('auth/signup', [
            '_csrf_token' => $_SESSION['_csrf_token'],
        ]);
    }


    public static function forgottenPassword()
    {
    }


    public static function resetPassword()
    {
    }
}
