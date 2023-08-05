<?php

namespace App\Models\Payments;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;

class PaymentAPICall
{
    private $client;
    private $clientId;
    private $clientSecret;
    private $apiUrl;
    private $username;
    private $password;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = 'https://paydev.appletechlabs.com/';
        $this->clientId = '2';
        $this->clientSecret = "2gpPOUHxIvoB2RC88EsTu7pZKqgBWA2xdKSCMw9Y";
        $this->username = "rifky@appletechlabs.com";
        $this->password = "123";
    }

    public function requestToken()
    {
        try {

            $response = $this->client->request('POST', $this->apiUrl . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'username' => $this->username,
                    'password' =>  $this->password,
                    'scope' => '',
                ]
            ]);

            $response = json_decode($response->getBody(), JSON_FORCE_OBJECT);
            $result = array('status' => true, 'data' => $response);
            return $response['access_token'];
        } catch (ClientException $ex) {
            if ($ex->hasResponse()) {
                $response = $ex->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $result = array('status' => false, 'data' => json_decode($responseBodyAsString, JSON_FORCE_OBJECT));
            } else {
                $result = array('status' => false, 'data' => []);
            }
            return $result;
        }
    }

    public function makeApiCallFormData($method, $url, $dataType, $data, $appType = 'traveller')
    {
        $token = $this->requestToken();

        // return $token;
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'app' => $appType
            ];

            if ($dataType === "form_data") {
                $dataKey = "form_params";
            } else {
                $dataKey = "json";
            }

            $response = $this->client->request($method, $this->apiUrl . $url, [
                'headers' => $headers,
                $dataKey => $data
            ]);
            $response = json_decode($response->getBody(), JSON_FORCE_OBJECT);

            return $response;
        } catch (ClientException $ex) {

            return $ex;
            // if ($ex->hasResponse()) {
            //     $response = $ex->getResponse();
            //     $responseBodyAsString = $response->getBody()->getContents();
            //     $result = array('status' => false, 'data' => json_decode($responseBodyAsString, JSON_FORCE_OBJECT));
            // } else {
            //     $result = array('status' => false, 'data' => []);
            // }
            // return $result;
        }
    }
}
