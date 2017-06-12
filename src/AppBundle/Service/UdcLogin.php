<?php
namespace AppBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class UdcLogin
{
    private $username;
    private $password;
    /**
     * @var Client
     */
    private $client;

    public function  __construct($username, $password, Client $client)
    {


        $this->username = $username;
        $this->password = $password;
        $this->client = new Client();
    }


    function udcLogin(){

        try {

            $body = 'test request';

            $response = $this->client->post(
                "/admin/udc_api_response/",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept'       => '/',
                    ],
                    'body'    => $body,
                ]
            );
            $content  = $response->getBody()->getContents();
            var_dump($content);
        } catch (RequestException $e) {
            var_dump($e->getRequest());
            if ($e->hasResponse()) {
                var_dump($e->getResponse()->getReasonPhrase());
            }
        }

    }
}