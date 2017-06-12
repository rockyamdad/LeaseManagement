<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;


class LoginListener implements EventSubscriberInterface
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /** @var  \Symfony\Component\HttpFoundation\Session\Session */
    private $session;


    public function __construct(EntityManager $entityManager, $session)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onSecurityImplicitLogin',
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        );
    }

    public function onSecurityImplicitLogin(UserEvent $event)
    {
        $user = $event->getUser();

        if ($user instanceof User) {
            $this->setUserOffice($user);
        }
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            $this->setUserOffice($user);
        }
    }

    private function setUserOffice(User $user)
    {
        if ($user->getOffice()) {
            $officeEntity = $user->getOffice();
            $office = array('districtId' => null, 'district' => null, 'upozilaId' => null, 'upozila' => null);

            $office['userId'] = $user->getId();
            $office['userRoles'] = $user->getRoles();
            $office['officeId'] = $officeEntity->getId();
            $office['name'] = $officeEntity->getName();
            if ($officeEntity->getDistrict()) {
                $office['districtId'] =  $officeEntity->getDistrict()->getId();
                $office['district'] = $officeEntity->getDistrict()->getName();
            }
            if ($officeEntity->getUpozila()) {
                $office['upozilaId'] = $officeEntity->getUpozila()->getId();
                $office['upozila'] = $officeEntity->getUpozila()->getName();
            }

            $office['type'] = $officeEntity->getType();
            $this->session->set('userOffice', $office);
        }
    }
}