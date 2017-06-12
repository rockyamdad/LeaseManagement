<?php
namespace AppBundle\Service;

use AppBundle\Entity\Office;
use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityManager;
use EasyBanglaDate\Types\BnDateTime;
use PorchaProcessingBundle\Entity\Report\EntryStatistics;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class AppManager
{
    use QueryAssistant;

    protected $em;
    protected $user = null;
    protected $office = null;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage) {

        $this->em = $entityManager;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->office = $this->user->getOffice();
    }


}