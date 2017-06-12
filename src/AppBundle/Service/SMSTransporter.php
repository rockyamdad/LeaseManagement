<?php

namespace AppBundle\Service;

class SMSTransporter
{
    private $endPoint;
    private $username;
    private $password;
    private $client;

    public function __construct($endpoint, $username, $password)
    {
        $this->endPoint = $endpoint;
        $this->username = $username;
        $this->password = $password;

        $this->client = new \GuzzleHttp\Client();
    }

    public function send($number, $message)
    {
        $param = array(
            'ms'       => $number,
            'txt'      => $message,
            'username' => $this->username,
            'password' => $this->password,
        );

       $request = $this->client->request('GET', $this->buildUrl($param));

        return (string) $request->getBody();
    }

    private function buildUrl($param)
    {
        return $this->endPoint . '?' . http_build_query($param);
    }

}