<?php

namespace UserBundle\Service;

class NessApiManager
{
    private $endPoint;
    private $username;
    private $password;
    private $client;
    private $requestUrl = null;

    public function __construct($endpoint, $username, $password)
    {
        $this->endPoint = $endpoint;
        $this->username = $username;
        $this->password = $password;

        //$this->client = new \GuzzleHttp\Client();
    }

    private function buildXml($param = array())
    {
        $data = '<?xml version="1.0" encoding="UTF-8"?>';
        $data .= '<submitdata>';
        $data .= '<par_data>';
        $data .= "<u_id><![CDATA[{$this->username}]]></u_id>";
        $data .= "<u_pass><![CDATA[{$this->password}]]></u_pass>";
        if (isset($param['org_id'])) {
            $data .= "<org_id><![CDATA[{$param['org_id']}]]></org_id>";
        }

        if (isset($param['user_name'])) {
            $data .= "<user_name><![CDATA[{$param['user_name']}]]></user_name>";
        }

        if (isset($param['user_pass'])) {
            $pass = md5($param['user_pass']);
            $data .= "<user_pass><![CDATA[{$pass}]]></user_pass>";
        }

        $data .= '</par_data>';
        $data .= '</submitdata>';

        return $data;
    }

    public function getOfficeList()
    {
        $this->requestUrl = $this->endPoint . 'get_office_list.php';
        return $this->send(array());
    }

    public function getUserList($orgId)
    {
        $this->requestUrl = $this->endPoint . 'get_office_user_list.php';
        return $this->send(array('org_id' => $orgId));
    }

    public function getUser($user, $password, $org_id)
    {
        $this->requestUrl = $this->endPoint . 'get_office_user_auth.php';
        $this->requestUrl = $this->endPoint . 'get_office_user_auth.php';
        return $this->send(array('user_name' => $user, 'user_pass' => $password, 'org_id' => $org_id));
    }

    public function getUserFormatted($user, $password, $organizationId)
    {
        if ($data = $this->getUser($user, $password, $organizationId)) {

            $data = simplexml_load_string($data);
            if (isset($data->orgdata)) {
                return $this->extractUserData($data->orgdata[0]);
            }
        }

        return false;
    }

    public function send($param)
    {
        $XMLString = $this->buildXml($param);

        if ($this->requestUrl) {

            /**
             * $request = $this->client->request('POST', $this->requestUrl, $param);
             */

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_URL, $this->requestUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "XML=".$XMLString);
            $content = trim(curl_exec($ch));

            return $content;
        }

        return false;
    }

    protected function convertXml($content, $type)
    {
        if ($type == 'array') {
            return json_decode(json_encode((array)simplexml_load_string($content)), 1);
        }

        return $content;
    }

    function extractUserData($row)
    {
        $data = array(
            'username' => (string)$row->{'tbl15_user_id'},
            'email' => (string)$row->{'tbl15_email'},
            'full_name_en' => (string)$row->{'tbl15_fullname_eng'},
            'full_name_bn' => (string)$row->{'tbl15_fullname_ban'},
            'fathers_full_name_en' => (string)$row->{'tbl15_fathername_eng'},
            'fathers_full_name_bn' => (string)$row->{'tbl15_fathername_ban'},
            'mothers_full_name_en' => (string)$row->{'tbl15_mothername_eng'},
            'mothers_full_name_bn' => (string)$row->{'tbl15_mothername_ban'},
            'gender' => (string)$row->{'tbl15_gender'},
            'nid' => (string)$row->{'tbl15_nid'},
            'cellphone' => (string)$row->{'tbl15_mobile_no'},
            'dob' => (string)$row->{'tbl15_birth_date'},
            'designation' => (string)$row->{'tbl15_designation_title'},
            'photo' => (string)$row->{'picturepath'},
            'signature' => (string)$row->{'signaturepath'},
        );

        return $data;
    }

}