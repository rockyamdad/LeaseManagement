<?php

namespace AppBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class UdcApiManager
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
        $this->client = $client;
    }


    function udcUserCreate()
    {
        try {
            $response = $this->client->get("/udc-api");
            echo $response->getBody();
            $content  = $response->getBody()->getContents();

            return $content;
        } catch (RequestException $e) {
            /*var_dump($e->getRequest());
            if ($e->hasResponse()) {
                var_dump($e->getResponse()->getReasonPhrase());
            }*/
        }
    }

    function udcUserGetInformation(){

        $offices = [
            [

                'name' => 'judc1',
                'active' => '1',
                'districtGeocode' => '38',
                'upozila' => '13',
                'union'=>1
            ],
            [
                'name' => 'judc2',
                'active' => '1',
                'districtGeocode' => '38',
                'upozila' => '13',
                'union'=>1
            ],
            [
                'name' => 'judc3',
                'active' => '1',
                'districtGeocode' => '38',
                'upozila' => '13',
                'union'=>2
            ],
            [
                'name' => 'judc4',
                'active' => '1',
                'districtGeocode' => '38',
                'upozila' => '47',
                'union'=>7
            ],
            [
                'name' => 'judc5',
                'active' => '1',
                'districtGeocode' => '38',
                'upozila' => '47',
                'union'=>8
            ],
            [
                'name' => 'nudc1',
                'active' => '1',
                'districtGeocode' => '73',
                'upozila' => '64',
            ],
            [
                'name' => 'nudc2',
                'active' => '1',
                'districtGeocode' => '73',
                'upozila' => '64',
            ],
            [
                'name' => 'nudc3',
                'active' => '1',
                'districtGeocode' => '73',
                'upozila' => '36',
            ],
            [
                'name' => 'nudc4',
                'active' => '1',
                'districtGeocode' => '73',
                'upozila' => '15',
            ],
            [
                'name' => 'nudc5',
                'active' => '1',
                'districtGeocode' => '38',
                'upozila' => '15',
            ]
        ];

        $users = [
            [
                'username' => 'judc1p1',
                'email' => 'judc1p1@cc.com',
                'office' => 'judc1',
            ],
            [
                'username' => 'judc1p2',
                'email' => 'judc1p2@cc.com',
                'office' => 'judc1',
            ],
            [
                'username' => 'judc2p1',
                'email' => 'judc2p1@cc.com',
                'office' => 'judc2',
            ],
            [
                'username' => 'judc2p2',
                'email' => 'judc2p2@cc.com',
                'office' => 'judc2',
            ],
            [
                'username' => 'judc3p1',
                'email' => 'judc3p1@cc.com',
                'office' => 'judc3',
            ],
            [
                'username' => 'nudc1p1',
                'email' => 'nudc1p1@cc.com',
                'office' => 'nudc1',
            ],
            [
                'username' => 'nudc1p2',
                'email' => 'nudc1p2@cc.com',
                'office' => 'nudc1',
            ],
            [
                'username' => 'nudc2p1',
                'email' => 'nudc2p1@cc.com',
                'office' => 'nudc2',
            ],
            [
                'username' => 'nudc2p2',
                'email' => 'nudc2p2@cc.com',
                'office' => 'nudc2',
            ],
            [
                'username' => 'nudc3p1',
                'email' => 'nudc3p1@cc.com',
                'office' => 'nudc3',
            ]
        ];
            $officeAndUserInfo = array(
                'offices'=>$offices,
                'users'=>$users,
            );
        return  $jsonUserData = json_encode($officeAndUserInfo);
    }

    public function udcTypeOfficesImport(){

        $offices = [
            [
                'refId'=>'judcoffice6',
                'name' => 'judcoffice6',
                'active' => '0',
                'districtGeocode' => '38',
                'upozila' => '13',
                'union'=>'1'
            ],
            [
                'refId'=>'judcoffice2',
                'name' => 'judcoffice2',
                'active' => '0',
                'districtGeocode' => '38',
                'upozila' => '13',
                'union'=>'2'
            ],
            [
                'refId'=>'judcoffice3',
                'name' => 'judcoffice3',
                'active' => '0',
                'districtGeocode' => '38',
                'upozila' => '13',
                'union'=>'2'
            ],
            [
                'refId'=>'judcoffice4',
                'name' => 'judcoffice4',
                'active' => '0',
                'districtGeocode' => '38',
                'upozila' => '47',
                'union'=>'76'
            ],
            [
                'refId'=>'judcoffice5',
                'name' => 'judcoffice5',
                'active' => '0',
                'districtGeocode' => '38',
                'upozila' => '47',
                'union'=>'85'
            ],
            [
                'refId'=>'nudcoffice1',
                'name' => 'nudcoffice1',
                'active' => '0',
                'districtGeocode' => '73',
                'upozila' => '12',
                'union'=>'1'
            ],
            [
                'refId'=>'nudcoffice2',
                'name' => 'nudcoffice2',
                'active' => '0',
                'districtGeocode' => '73',
                'upozila' => '12',
                'union'=>'2'
            ],
            [
                'refId'=>'nudcoffice3',
                'name' => 'nudcoffice3',
                'active' => '0',
                'districtGeocode' => '73',
                'upozila' => '12',
                'union'=>'9'
            ],
            [
                'refId'=>'nudcoffice4',
                'name' => 'nudcoffice4',
                'active' => '0',
                'districtGeocode' => '73',
                'upozila' => '85',
                'union'=>'67'
            ],
            [
                'refId'=>'nudcoffice5',
                'name' => 'nudcoffice5',
                'active' => '0',
                'districtGeocode' => '38',
                'upozila' => '85',
                'union'=>'98'
            ]
        ];
        return  $jsonUdcTypeOfficesData = json_encode($offices);
    }
    public function udcLoginInfoResponse($data){
      //  $response  = $this->client->request('POST', '/udc-api-login-response',$data);

        //post Data
        $users = [['refId'=>'judcoffice1']];
        return  $jsonUdcTypeOfficesData = json_encode($users);
    }

}