<?php
// src/AppBundle/Twig/AppExtension.php
namespace LeaseBundle\Twig;

use DateTimeZone;
use EasyBanglaDate\Types\BnDateTime;
use Twig_Extension;

class CustomTwigExtensionForLease extends Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('convertToBnDate', array($this, 'convertToBnDate')),
        );
    }

    public function convertToBnDate($date)
    {
        $bongabda = new BnDateTime($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Dhaka'));
        $bongabda->setMorning(0);
        //return $bongabda->format('l jS F Y b h:i:s');

        return $bongabda->format('jS F Y');

    }

    public function getName()
    {
        return 'custom_twig_extension_for_lease';
    }
}