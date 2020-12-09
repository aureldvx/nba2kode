<?php

namespace App\Controller;

use App\Utils\QueryBuilder;
use App\Utils\Request;
use App\Utils\Response;
use App\Utils\Services;
use DateTime;
use Exception;
use PDO;

class AdminController extends AbstractController
{
    /**
     * Listing de tous les matchs.
     *
     * @return Exception|mixed
     */
    public static function index()
    {
        $user = Services::getUser();
        $matches = (new QueryBuilder())
            ->select()
            ->inTable('matches')
            ->getQuery()
            ->getResult()
        ;

        return AbstractController::renderView('matches/listing', [
            'matches' => $matches,
            'user' => $user,
        ]);
    }


    /**
     * Suppression d'un match donné.
     */
    public static function delete()
    {
        $user = Services::getUser();
        $request = Request::explodeUri($_SERVER['REQUEST_URI']);

        $matchId = (new QueryBuilder())
            ->select('teams_home_name')
            ->inTable('matches')
            ->where('id', '=', 'id')
            ->setParameters([['id', (int)$request['params']['match'], PDO::PARAM_INT]])
            ->getQuery()
            ->getResult()
        ;

        if (count($matchId) > 0) {
            (new QueryBuilder())
                ->delete()
                ->inTable('matches')
                ->where('id', '=', 'id')
                ->setParameters([['id', (int)$request['params']['match'], PDO::PARAM_INT]])
                ->getQuery()
                ->getResult()
            ;
        } else {
            Services::addFlash('error', 'Aucun match trouvé avec cet identifiant');
        }

        (new Response())->redirectToRoute('/admin');
    }


    /**
     * Mise à jour d'un match donné.
     *
     * @return Exception|mixed
     * @throws Exception
     */
    public static function edit()
    {
        $user = Services::getUser();
        $request = Request::explodeUri($_SERVER['REQUEST_URI']);

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($request['params']['match'])) {
            Services::addFlash('error', 'Aucun match trouvé avec cet identifiant');
            (new Response())->redirectToRoute('/admin');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $matchId = (int)Services::sanitizeEntry($request['params']['match'], FILTER_SANITIZE_NUMBER_INT);
        } else {
            $matchId = (int)Services::sanitizeEntry($_POST['match_id'], FILTER_SANITIZE_NUMBER_INT);
        }

        $results = (new QueryBuilder())
            ->select(['teams_home_name'])
            ->inTable('matches')
            ->getQuery()
            ->getResult()
        ;

        $teams = [];
        foreach ($results as $team) {
            $teams[] = $team->teams_home_name;
        }
        $teams = array_unique($teams);
        sort($teams);

        $actual = (new QueryBuilder())
            ->select(
                [
                    'id',
                    'play_date',
                    'teams_home_name',
                    'teams_home_logo',
                    'teams_away_name',
                    'teams_away_logo',
                    'score_home_total',
                    'score_away_total'
                ]
            )
            ->inTable('matches')
            ->where('id', '=', 'id')
            ->setParameters([['id', $matchId, PDO::PARAM_INT]])
            ->getQuery()
            ->getResult()
        ;
        $actual = $actual[0];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $playDate = Services::sanitizeEntry($_POST['play_date'], FILTER_SANITIZE_STRING);
            $homeTeam = Services::sanitizeEntry($_POST['home_team'], FILTER_SANITIZE_STRING);
            $homeScore = (int)Services::sanitizeEntry($_POST['home_score'], FILTER_SANITIZE_NUMBER_INT);
            $awayTeam = Services::sanitizeEntry($_POST['away_team'], FILTER_SANITIZE_STRING);
            $awayScore = (int)Services::sanitizeEntry($_POST['away_score'], FILTER_SANITIZE_NUMBER_INT);
            $token = Services::sanitizeEntry($_POST['_csrf_token'], FILTER_UNSAFE_RAW);

            $errors = false;
            if (
                !isset($playDate) ||
                !isset($homeTeam) ||
                !isset($homeScore) ||
                !isset($awayTeam) ||
                !isset($awayScore)
            ) {
                Services::addFlash('error', 'Il est nécessaire de remplir tous les champs');
                $errors = true;
            }

            if (!in_array($homeTeam, $teams) || !in_array($awayTeam, $teams)) {
                Services::addFlash('error', 'Une des deux équipes concernées n\'existe pas');
                $errors = true;
            }

            try {
                new \DateTime($playDate, new \DateTimeZone('Europe/Paris'));
            } catch (Exception $e) {
                Services::addFlash('error', 'La date renseignée n\'a pas le format adapté');
                $errors = true;
            }

            if ($token !== $_SESSION['_csrf_token']) {
                Services::addFlash('error', 'Une erreur est survenue lors de la création. Veuillez réessayer');
                $errors = true;
            }

            if (!$errors) {
                $homeLogo = (new QueryBuilder())
                    ->select('teams_home_logo')
                    ->inTable('matches')
                    ->where('teams_home_name', '=', 'name')
                    ->limit(1)
                    ->setParameters([[':name', $homeTeam]])
                    ->getQuery()
                    ->getResult()
                ;
                $homeLogo = $homeLogo[0]->teams_home_logo;

                $awayLogo = (new QueryBuilder())
                    ->select('teams_away_logo')
                    ->inTable('matches')
                    ->where('teams_away_name', '=', 'name')
                    ->limit(1)
                    ->setParameters([[':name', $awayTeam]])
                    ->getQuery()
                    ->getResult()
                ;
                $awayLogo = $awayLogo[0]->teams_away_logo;

                try {
                    (new QueryBuilder())
                        ->update(
                            [
                                'play_date' => ':play_date',
                                'teams_home_name' => ':teams_home_name',
                                'teams_home_logo' => ':teams_home_logo',
                                'score_home_total' => ':score_home_total',
                                'teams_away_name' => ':teams_away_name',
                                'teams_away_logo' => ':teams_away_logo',
                                'score_away_total' => ':score_away_total',
                                'updated_at' => ':updated_at'
                            ]
                        )
                        ->inTable('matches')
                        ->where('id', '=', 'id')
                        ->setParameters(
                            [
                                ['id', $matchId, PDO::PARAM_INT],
                                [':play_date', $playDate, PDO::PARAM_STR],
                                [':teams_home_name', $homeTeam, PDO::PARAM_STR],
                                [':teams_home_logo', $homeLogo, PDO::PARAM_STR],
                                [':score_home_total', $homeScore, PDO::PARAM_INT],
                                [':teams_away_name', $awayTeam, PDO::PARAM_STR],
                                [':teams_away_logo', $awayLogo, PDO::PARAM_STR],
                                [':score_away_total', $awayScore, PDO::PARAM_INT],
                                [':updated_at', (new DateTime('now', new \DateTimeZone('Europe/Paris')))->format('Y-m-d H:i:s'), PDO::PARAM_STR],
                            ]
                        )
                        ->getQuery()
                        ->getResult()
                    ;

                    (new Response())->redirectToRoute('/admin');
                } catch (Exception $e) {
                    Services::addFlash('error', 'Une erreur est survenue lors la création du match. Veuillez réessayer.');
                }
            }
        }

        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));

        return AbstractController::renderView('matches/edit', [
            'teams' => $teams,
            'actual' => $actual,
            'user' => $user,
            '_csrf_token' => $_SESSION['_csrf_token'],
        ]);
    }


    /**
     * Création d'un nouveau match.
     *
     * @return Exception|mixed
     * @throws Exception
     */
    public static function create()
    {
        $user = Services::getUser();

        $results = (new QueryBuilder())
            ->select('teams_home_name')
            ->inTable('matches')
            ->getQuery()
            ->getResult()
        ;

        $teams = [];
        foreach ($results as $team) {
            $teams[] = $team->teams_home_name;
        }
        $teams = array_unique($teams);
        sort($teams);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $playDate = Services::sanitizeEntry($_POST['play_date'], FILTER_SANITIZE_STRING);
            $homeTeam = Services::sanitizeEntry($_POST['home_team'], FILTER_SANITIZE_STRING);
            $homeScore = (int)Services::sanitizeEntry($_POST['home_score'], FILTER_SANITIZE_NUMBER_INT);
            $awayTeam = Services::sanitizeEntry($_POST['away_team'], FILTER_SANITIZE_STRING);
            $awayScore = (int)Services::sanitizeEntry($_POST['away_score'], FILTER_SANITIZE_NUMBER_INT);
            $token = Services::sanitizeEntry($_POST['_csrf_token'], FILTER_UNSAFE_RAW);

            $errors = false;
            if (
                !isset($playDate) ||
                !isset($homeTeam) ||
                !isset($homeScore) ||
                !isset($awayTeam) ||
                !isset($awayScore)
            ) {
                Services::addFlash('error', 'Il est nécessaire de remplir tous les champs');
                $errors = true;
            }

            if (!in_array($homeTeam, $teams) || !in_array($awayTeam, $teams)) {
                Services::addFlash('error', 'Une des deux équipes concernées n\'existe pas');
                $errors = true;
            }

            try {
                new \DateTime($playDate, new \DateTimeZone('Europe/Paris'));
            } catch (Exception $e) {
                Services::addFlash('error', 'La date renseignée n\'a pas le format adapté');
                $errors = true;
            }

            if ($token !== $_SESSION['_csrf_token']) {
                Services::addFlash('error', 'Une erreur est survenue lors de la création. Veuillez réessayer');
                $errors = true;
            }

            if (!$errors) {
                $homeLogo = (new QueryBuilder())
                    ->select('teams_home_logo')
                    ->inTable('matches')
                    ->where('teams_home_name', '=', 'name')
                    ->limit(1)
                    ->setParameters([[':name', $homeTeam]])
                    ->getQuery()
                    ->getResult()
                ;
                $homeLogo = $homeLogo[0]->teams_home_logo;

                $awayLogo = (new QueryBuilder())
                    ->select('teams_away_logo')
                    ->inTable('matches')
                    ->where('teams_away_name', '=', 'name')
                    ->limit(1)
                    ->setParameters([[':name', $awayTeam]])
                    ->getQuery()
                    ->getResult()
                ;
                $awayLogo = $awayLogo[0]->teams_away_logo;

                try {
                    (new QueryBuilder())
                        ->insert(
                            [
                                'play_date' => ':play_date',
                                'teams_home_name' => ':teams_home_name',
                                'teams_home_logo' => ':teams_home_logo',
                                'score_home_total' => ':score_home_total',
                                'teams_away_name' => ':teams_away_name',
                                'teams_away_logo' => ':teams_away_logo',
                                'score_away_total' => ':score_away_total',
                                'updated_at' => ':updated_at'
                            ]
                        )
                        ->inTable('matches')
                        ->setParameters(
                            [
                                [':play_date', $playDate, PDO::PARAM_STR],
                                [':teams_home_name', $homeTeam, PDO::PARAM_STR],
                                [':teams_home_logo', $homeLogo, PDO::PARAM_STR],
                                [':score_home_total', $homeScore, PDO::PARAM_INT],
                                [':teams_away_name', $awayTeam, PDO::PARAM_STR],
                                [':teams_away_logo', $awayLogo, PDO::PARAM_STR],
                                [':score_away_total', $awayScore, PDO::PARAM_INT],
                                [':updated_at', (new DateTime('now', new \DateTimeZone('Europe/Paris')))->format('Y-m-d H:i:s'), PDO::PARAM_STR],
                            ]
                        )
                        ->getQuery()
                        ->getResult()
                    ;

                    (new Response())->redirectToRoute('/admin');
                } catch (Exception $e) {
                    Services::addFlash('error', 'Une erreur est survenue lors la création du match. Veuillez réessayer.');
                }
            }
        }

        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));

        return AbstractController::renderView('matches/create', [
            'teams' => $teams,
            'user' => $user,
            '_csrf_token' => $_SESSION['_csrf_token'],
        ]);
    }
}
