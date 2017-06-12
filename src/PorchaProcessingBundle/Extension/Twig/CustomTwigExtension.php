<?php
namespace PorchaProcessingBundle\Extension\Twig;

use Doctrine\ORM\EntityManager;
use EasyBanglaDate\Types\BnDateTime;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Util\BanglaTrans;
use PorchaProcessingBundle\Util\PlaceHolders;
use Symfony\Component\DependencyInjection\Container;

class CustomTwigExtension extends \Twig_Extension
{
    protected $placeholderReplace;
    protected $container;

    public function __construct(Container $container, $placeholderReplace)
    {
        $this->container = $container;
        $this->placeholderReplace = $placeholderReplace;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('enumToHumanize', array($this, 'enumToHumanize')),
            new \Twig_SimpleFilter('numberBanglaConvert', array($this, 'numberBanglaConvert')),
            new \Twig_SimpleFilter('toBanglaDate', array($this, 'toBanglaDate')),
        );
    }

    public function getFunctions() {

        return array(
            new \Twig_SimpleFunction('placeholderReplaceWithFields', array($this, 'placeholderReplaceWithFields'), array('is_safe'=>array('html'))),
            new \Twig_SimpleFunction('placeholderReplaceWithData', array($this, 'placeholderReplaceWithData'), array('is_safe'=>array('html'))),
            new \Twig_SimpleFunction('placeholderReplaceWithTooltip', array($this, 'placeholderReplaceWithTooltip'), array('is_safe'=>array('html'))),
            new \Twig_SimpleFunction('getPlaceHolders', array($this, 'getPlaceHolders')),

            new \Twig_SimpleFunction('nonEntryKhatianCountByVolume', array($this, 'nonEntryKhatianCountByVolume'), array('is_safe'=>array('html'))),
            new \Twig_SimpleFunction('approvedKhatianCountByVolume', array($this, 'approvedKhatianCountByVolume'), array('is_safe'=>array('html'))),
            new \Twig_SimpleFunction('numberBanglaConvert', array($this, 'numberBanglaConvert'), array('is_safe'=>array('html'))),

            new \Twig_SimpleFunction('draftKhatianCount', array($this, 'draftKhatianCount'), array('is_safe'=>array('html'))),
            new \Twig_SimpleFunction('sentKhatianCount', array($this, 'sentKhatianCount'), array('is_safe'=>array('html'))),
            new \Twig_SimpleFunction('reAssignedKhatianCount', array($this, 'reAssignedKhatianCount'), array('is_safe'=>array('html'))),
            new \Twig_SimpleFunction('verifyNewKhatianCount', array($this, 'verifyNewKhatianCount'), array('is_safe'=>array('html'))),
            new \Twig_SimpleFunction('compareNewKhatianCount', array($this, 'compareNewKhatianCount'), array('is_safe'=>array('html'))),
            new \Twig_SimpleFunction('approveNewKhatianCount', array($this, 'approveNewKhatianCount'), array('is_safe'=>array('html'))),
            new \Twig_SimpleFunction('approvedKhatianCount', array($this, 'approvedKhatianCount'), array('is_safe'=>array('html'))),

            new \Twig_SimpleFunction('isVolumeDeletable', array($this, 'isVolumeDeletable'), array('is_safe'=>array('html'))),
            new \Twig_SimpleFunction('jlnumberBySurveyType', array($this, 'jlnumberBySurveyType'), array('is_safe'=>array('html'))),


        );
    }

    public function nonEntryKhatianCountByVolume($volume) {
        return $this->container->get('porcha_processing.service.khatian_manager')->getNonEntryKhatianCountByVolume($volume);
    }

    public function approvedKhatianCountByVolume($volume) {
        return $this->container->get('porcha_processing.service.volume_manager')->approvedKhatianCount($volume);
    }

    public function placeholderReplaceWithFields($str, $khatianPage)
    {
        return $this->placeholderReplace->replaceWithFields($str, $khatianPage);
    }

    public function isVolumeDeletable($volume)
    {
        return $this->container->get('porcha_processing.service.volume_manager')->isVolumeDeletable($volume);
    }

    public function placeholderReplaceWithData($str, $khatianPage, $queryParams = array())
    {
        return $this->placeholderReplace->replaceWithData($str, $khatianPage, $queryParams);
    }

    public function placeholderReplaceWithTooltip($str, $khatianPage)
    {
        return $this->placeholderReplace->replaceWithTooltip($str, $khatianPage);
    }

    public function getPlaceHolders()
    {
        return PlaceHolders::getAll();
    }

    public function enumToHumanize($str)
    {
        return ucwords(str_replace('_', ' ', strtolower($str)));
    }

    public function approvedKhatianCount($volume)
    {
        return $this->numberBanglaConvert($this->container->get('porcha_processing.service.volume_manager')->approvedKhatianCount($volume));
    }

    public function numberBanglaConvert($number)
    {
        return PlaceHolders::numberConvert($number);
    }

    public function draftKhatianCount($sr = false) {
        return $this->numberBanglaConvert($this->container->get('porcha_processing.service.workflow_manager')->draftKhatianCount($sr));
    }

    public function sentKhatianCount($sr = false) {
        return $this->numberBanglaConvert($this->container->get('porcha_processing.service.workflow_manager')->sentKhatianCount($sr));
    }

    public function reAssignedKhatianCount($sr = false) {
        return $this->numberBanglaConvert($this->container->get('porcha_processing.service.workflow_manager')->reAssignedKhatianCount($sr));
    }

    public function verifyNewKhatianCount($sr = false) {
        return $this->numberBanglaConvert($this->container->get('porcha_processing.service.workflow_manager')->verifyNewKhatianCount($sr));
    }

    public function compareNewKhatianCount($sr = false) {
        return $this->numberBanglaConvert($this->container->get('porcha_processing.service.workflow_manager')->compareNewKhatianCount($sr));
    }

    public function approveNewKhatianCount($sr = false) {
        return $this->numberBanglaConvert($this->container->get('porcha_processing.service.workflow_manager')->approveNewKhatianCount($sr));
    }

    public function toBanglaDate($date, $format = 'j F Y')
    {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }
        return (new BnDateTime($date->format('d M y')))->getDateTime()->format($format);
    }

    public function jlnumberBySurveyType(Mouza $mouza, $surveyType) {
        return $this->container->get('porcha_processing.service.volume_manager')->getJlnumberBySurveyType($mouza, $surveyType);
    }

    public function getName()
    {
        return 'custom_twig_extension';
    }
} 