<?php

namespace App\Controller;

use App\Utils\QueryBuilder;
use App\Utils\Response;
use App\Utils\Services;
use DateTime;
use Exception;
use PDO;

class ApiController extends AbstractController
{

    /**
     * Génération d'un nouvelle clé API.
     *
     * @throws Exception
     */
    public static function generateKey()
    {
        $user = Services::getUser();
        $apiKey = bin2hex(random_bytes(32));

        try {
            (new QueryBuilder())
                ->update(
                    [
                        'api_key' => ':api_key',
                        'updated_at' => ':updated_at'
                    ]
                )
                ->inTable('usrs')
                ->where('id', '=', 'id')
                ->setParameters(
                    [
                        [':api_key', $apiKey, PDO::PARAM_STR],
                        [':updated_at', (new DateTime('now', new \DateTimeZone('Europe/Paris')))->format('Y-m-d H:i:s'), PDO::PARAM_STR],
                        [':id', (int)$user->id, PDO::PARAM_INT],
                    ]
                )
                ->getQuery()
                ->getResult()
            ;

            Services::addFlash('success', "Votre clé d'API est : $apiKey");
            (new Response())->redirectToRoute('/admin');
        } catch (Exception $e) {
            Services::addFlash('error', 'Une erreur est survenue lors de la génération de la clé');
        }
    }


    /**
     * Renvoi de la liste de tous les matchs.
     */
    public static function getAllMatches()
    {
        $request = json_decode(file_get_contents('php://input'));
        if (self::verifyRequest($request)) {
            $matches = (new QueryBuilder())
                ->select(
                    [
                        'play_date',
                        'teams_home_name',
                        'teams_home_logo',
                        'score_home_total',
                        'teams_away_name',
                        'teams_away_logo',
                        'score_away_total'
                    ]
                )
                ->inTable('matches')
                ->getQuery()
                ->getResult()
            ;
            print_r($matches);
        }
    }


    /**
     * Création d'un nouveau match via l'API.
     *
     * @return string|true
     */
    public static function addMatch()
    {
        $request = json_decode(file_get_contents('php://input'));
        if (self::verifyRequest($request)) {
            $errors = [];
            if (isset($request->match)) {
                if (
                    !isset($request->match->playDate) ||
                    !isset($request->match->homeTeam) ||
                    !isset($request->match->homeScore) ||
                    !isset($request->match->awayTeam) ||
                    !isset($request->match->awayScore)
                ) {
                    $errors[] = 'Le format envoyé n\'est pas correct. Vérifiez la présence de toutes les clés';
                    return print_r($errors);
                }

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

                $playDate = Services::sanitizeEntry($request->match->playDate, FILTER_SANITIZE_STRING);
                $homeTeam = Services::sanitizeEntry($request->match->homeTeam, FILTER_SANITIZE_STRING);
                $homeScore = (int)Services::sanitizeEntry($request->match->homeScore, FILTER_SANITIZE_NUMBER_INT);
                $awayTeam = Services::sanitizeEntry($request->match->awayTeam, FILTER_SANITIZE_STRING);
                $awayScore = (int)Services::sanitizeEntry($request->match->awayScore, FILTER_SANITIZE_NUMBER_INT);

                if (!in_array($homeTeam, $teams) || !in_array($awayTeam, $teams)) {
                    $errors[] = 'Une des deux équipes concernées n\'existe pas';
                    return print_r($errors);
                }

                try {
                    new DateTime($playDate, new \DateTimeZone('Europe/Paris'));
                } catch (\Exception $e) {
                    $errors[] = 'La date renseignée n\'a pas le format adapté';
                    return print_r($errors);
                }

                if (count($errors) === 0) {
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
                                    [':play_date', (new DateTime($playDate, new \DateTimeZone('Europe/Paris')))->format('Y-m-d H:i:s'), PDO::PARAM_STR],
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
                        echo 'SUCCESS';
                    } catch (Exception $e) {
                        echo $e;
                    }
                }
            } else {
                $errors[] = 'Une clé `match` est nécessaire pour ajouter un match';
                return print_r($errors);
            }
        }
    }


    /**
     * Suppression d'un match via l'API.
     */
    public static function deleteMatch()
    {
        $request = json_decode(file_get_contents('php://input'));
        if (self::verifyRequest($request)) {
            $matchId = (int)Services::sanitizeEntry($request->matchId, FILTER_SANITIZE_STRING);
            $matchExists = (new QueryBuilder())
                ->select(['teams_home_name'])
                ->inTable('matches')
                ->where('id', '=', 'id')
                ->limit(1)
                ->setParameters([[':id', $matchId, PDO::PARAM_INT]])
                ->getQuery()
                ->getResult()
            ;

            if (count($matchExists) > 0) {
                try {
                    (new QueryBuilder())
                        ->delete()
                        ->inTable('matches')
                        ->where('id', '=', 'id')
                        ->setParameters([[':id', $matchId, PDO::PARAM_INT]])
                        ->getQuery()
                        ->getResult()
                    ;
                    echo 'SUCCESS';
                } catch (Exception $e) {
                    echo $e;
                }
            }
        }
    }


    /**
     * Vérification de la bonne signature lors de l'appel.
     *
     * @param $request
     *
     * @return bool
     */
    private static function verifyRequest($request)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $apiKey = Services::sanitizeEntry($request->apiKey, FILTER_SANITIZE_STRING);

            try {
                $query = (new QueryBuilder())
                    ->select(['id'])
                    ->inTable('usrs')
                    ->where('api_key', '=', 'api_key')
                    ->setParameters([[':api_key', $apiKey, PDO::PARAM_STR]])
                    ->getQuery()
                    ->getResult()
                ;

                if (count($query) > 0) {
                    return true;
                } else {
                    echo "La clé d'API n'est pas valide";
                    return false;
                }
            } catch (Exception $e) {
                echo $e;
                return false;
            }
        }

        return false;
    }
}
