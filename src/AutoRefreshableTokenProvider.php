<?php

namespace Spatie\Dropbox;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class AutoRefreshableTokenProvider extends Client
{
    protected   $newAccessToken = null;
    /**
     * @return bool Whether the token was refreshed.
     */
    public function __construct($REFRESH_TOKEN)
    {
        $url = 'https://api.dropbox.com/oauth2/token';

        $client = new Client();
        try {
            $response = $client->post($url, [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $REFRESH_TOKEN,
                    'client_id' => env('DROPBOX_CLIENT_ID'),
                    'client_secret' => env('DROPBOX_APP_SECRET'),
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (isset($responseData['access_token'])) {
                $this->newAccessToken = $responseData['access_token'];
            } else {
                throw new Exception('Token refresh failed: ' . $responseData['error']);
            }
        } catch (ClientException $exception) {
            throw new Exception('Token refresh request failed: ' . $exception->getMessage());
        }
    }
    public function getRefreshToken(): string
    {
        return $this->newAccessToken;
    }
}
