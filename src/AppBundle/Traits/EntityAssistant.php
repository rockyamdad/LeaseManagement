<?php
namespace AppBundle\Traits;

trait EntityAssistant
{
    private function  convertNumber($transform, $number) {

        $arrayEn = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
        $arrayBn = array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");

        if ($transform == 'en2bn') {
            return str_replace($arrayEn, $arrayBn, $number);
        }

        return str_replace($arrayBn, $arrayEn, $number);
    }

    public function sanitizePhoneNumber($phoneNo)
    {
        $prefix = !$this->checkPhoneNumberWithCountryCode($phoneNo) ? '+88' : '';
        return $prefix . $phoneNo;
    }

    public function checkPhoneNumberWithCountryCode($userCellPhoneNumber)
    {
        preg_match('/(0|\+?\d{2})(\d{7,8})/', $userCellPhoneNumber, $matches);

        if (empty($matches)) {
            return false;
        }

        if ($matches[1] == '+88') {
            return $matches[1];
        }

        return false;
    }
}