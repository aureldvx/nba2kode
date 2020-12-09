<?php

namespace App\Controller;

use App\Utils\DatabaseInterface;
use App\Utils\QueryBuilder;
use App\Utils\Services;
use DateTime;
use Exception;
use PDO;

class ImportController extends DatabaseInterface
{

    /**
     * Base function for external API calls.
     *
     * @param string $endpoint
     *
     * @return object|Exception
     */
    private static function call(string $endpoint)
    {
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL            => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_HTTPHEADER     => [
                    'x-rapidapi-host: ' . Services::getEnv('RAPID_API_HOST'),
                    'x-rapidapi-key: ' . Services::getEnv('RAPID_API_KEY')
                ],
            ]
        );

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return new Exception('cURL Error #:' . $err);
        } else {
            return json_decode($response);
        }
    }


    /**
     * API call and database insertion initiator.
     *
     * @return Exception|void
     */
    public static function importDataFromApi()
    {
        try {
            $call = self::call('https://api-basketball.p.rapidapi.com/games?season=2019-2020&league=12');
            return self::createImportTable($call->response);
        } catch (Exception $e) {
            return new Exception("Une erreur est survenue lors de l'import des données de l'API : $e");
        }
    }


    /**
     * Create importation command from API.
     * 1 - Drop table
     * 2 - Recreate table structure
     * 3 - Insert API data into the new table
     *
     * @param array $rows
     *
     * @return Exception|void
     */
    private static function createImportTable(array $rows)
    {
        try {
            (new QueryBuilder())
                ->raw('DROP TABLE IF EXISTS matches;')
                ->getQuery()
                ->getResult()
            ;

            (new QueryBuilder())
                ->raw(
                    'CREATE TABLE IF NOT EXISTS matches (
                    id INTEGER PRIMARY KEY AUTO_INCREMENT,
                    play_date DATETIME,
                    status_long VARCHAR(50) NULL,
                    status_short VARCHAR(50) NULL,
                    league_id INTEGER NULL,
                    league_name VARCHAR(50) NULL,
                    league_season VARCHAR(9) NULL,
                    league_logo VARCHAR(255) NULL,
                    country_id INTEGER NULL,
                    country_name VARCHAR(10) NULL,
                    country_code VARCHAR(3) NULL,
                    country_flag VARCHAR(255) NULL,
                    teams_home_id INTEGER NULL,
                    teams_home_name VARCHAR(50),
                    teams_home_logo VARCHAR(255),
                    teams_away_id INTEGER NULL,
                    teams_away_name VARCHAR(50),
                    teams_away_logo VARCHAR(255),
                    score_home_quarter1 INTEGER NULL,
                    score_home_quarter2 INTEGER NULL,
                    score_home_quarter3 INTEGER NULL,
                    score_home_quarter4 INTEGER NULL,
                    score_home_overtime INTEGER NULL,
                    score_home_total INTEGER NULL,
                    score_away_quarter1 INTEGER NULL,
                    score_away_quarter2 INTEGER NULL,
                    score_away_quarter3 INTEGER NULL,
                    score_away_quarter4 INTEGER NULL,
                    score_away_overtime INTEGER NULL,
                    score_away_total INTEGER NULL,
                    updated_at DATETIME
                );'
                )
                ->getQuery()
                ->getResult()
            ;

            foreach ($rows as $row) {
                self::createQueryForRow($row);
            }
        } catch (Exception $exception) {
            return new Exception("Une erreur s'est produit lors de l'import des données de l'API : $exception");
        }
    }


    /**
     * Create unique insert query for each data row.
     *
     * @param object $row
     *
     * @return void
     * @throws Exception
     */
    private static function createQueryForRow(object $row)
    {
        (new QueryBuilder())
            ->insert([
                'play_date' => ':play_date',
                'status_long' => ':status_long',
                'status_short' => ':status_short',
                'league_id' => ':league_id',
                'league_name' => ':league_name',
                'league_season' => ':league_season',
                'league_logo' => ':league_logo',
                'country_id' => ':country_id',
                'country_name' => ':country_name',
                'country_code' => ':country_code',
                'country_flag' => ':country_flag',
                'teams_home_id' => ':teams_home_id',
                'teams_home_name' => ':teams_home_name',
                'teams_home_logo' => ':teams_home_logo',
                'teams_away_id' => ':teams_away_id',
                'teams_away_name' => ':teams_away_name',
                'teams_away_logo' => ':teams_away_logo',
                'score_home_quarter1' => ':score_home_quarter1',
                'score_home_quarter2' => ':score_home_quarter2',
                'score_home_quarter3' => ':score_home_quarter3',
                'score_home_quarter4' => ':score_home_quarter4',
                'score_home_overtime' => ':score_home_overtime',
                'score_home_total' => ':score_home_total',
                'score_away_quarter1' => ':score_away_quarter1',
                'score_away_quarter2' => ':score_away_quarter2',
                'score_away_quarter3' => ':score_away_quarter3',
                'score_away_quarter4' => ':score_away_quarter4',
                'score_away_overtime' => ':score_away_overtime',
                'score_away_total' => ':score_away_total',
                'updated_at' => ':updated_at'
            ])
            ->inTable('matches')
            ->setParameters([
                [':play_date', (new DateTime($row->date, new \DateTimeZone('Europe/Paris')))->format('Y-m-d H:i:s')],
                [':status_long', $row->status->long, PDO::PARAM_STR],
                [':status_short', $row->status->short, PDO::PARAM_STR],
                [':league_id', $row->league->id, PDO::PARAM_INT],
                [':league_name', $row->league->name, PDO::PARAM_STR],
                [':league_season', $row->league->season, PDO::PARAM_STR],
                [':league_logo', $row->league->logo, PDO::PARAM_STR],
                [':country_id', $row->country->id, PDO::PARAM_INT],
                [':country_name', $row->country->name, PDO::PARAM_STR],
                [':country_code', $row->country->code, PDO::PARAM_STR],
                [':country_flag', $row->country->flag, PDO::PARAM_STR],
                [':teams_home_id', $row->teams->home->id, PDO::PARAM_INT],
                [':teams_home_name', $row->teams->home->name, PDO::PARAM_STR],
                [':teams_home_logo', $row->teams->home->logo, PDO::PARAM_STR],
                [':teams_away_id', $row->teams->away->id, PDO::PARAM_INT],
                [':teams_away_name', $row->teams->away->name, PDO::PARAM_STR],
                [':teams_away_logo', $row->teams->away->logo, PDO::PARAM_STR],
                [':score_home_quarter1', $row->scores->home->quarter_1 === '' ? null : $row->scores->home->quarter_1],
                [':score_home_quarter2', $row->scores->home->quarter_2 === '' ? null : $row->scores->home->quarter_2],
                [':score_home_quarter3', $row->scores->home->quarter_3 === '' ? null : $row->scores->home->quarter_3],
                [':score_home_quarter4', $row->scores->home->quarter_4 === '' ? null : $row->scores->home->quarter_4],
                [':score_home_overtime', $row->scores->home->over_time === '' ? null : $row->scores->home->over_time],
                [':score_home_total', $row->scores->home->total === '' ? null : $row->scores->home->total],
                [':score_away_quarter1', $row->scores->away->quarter_1 === '' ? null : $row->scores->away->quarter_1],
                [':score_away_quarter2', $row->scores->away->quarter_2 === '' ? null : $row->scores->away->quarter_2],
                [':score_away_quarter3', $row->scores->away->quarter_3 === '' ? null : $row->scores->away->quarter_3],
                [':score_away_quarter4', $row->scores->away->quarter_4 === '' ? null : $row->scores->away->quarter_4],
                [':score_away_overtime', $row->scores->away->over_time === '' ? null : $row->scores->away->over_time],
                [':score_away_total', $row->scores->away->total === '' ? null : $row->scores->away->total],
                [':updated_at', (new DateTime())->format('Y-m-d H:i:s')]
            ])
            ->getQuery()
            ->getResult()
        ;
    }

}
