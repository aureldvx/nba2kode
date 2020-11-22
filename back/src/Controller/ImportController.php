<?php

namespace App\Controller;

use App\Utils\Services;
use Exception;

class ImportController extends Services
{

    /**
     * Base function for external API calls.
     *
     * @param string $endpoint
     *
     * @return mixed|Exception
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

    public static function importTeamStats()
    {
        $call = self::call('https://api-basketball.p.rapidapi.com/statistics?league=12&season=2019-2020&team=133');
        return $call->response;
    }
}
