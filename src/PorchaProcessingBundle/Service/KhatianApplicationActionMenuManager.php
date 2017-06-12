<?php
namespace PorchaProcessingBundle\Service;

use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityManager;
use Gedmo\Mapping\ExtensionODMTest;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\KhatianLog;
use PorchaProcessingBundle\Entity\KhatianPage;
use PorchaProcessingBundle\Entity\KhatianVersion;
use PorchaProcessingBundle\Entity\Volume;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use UserBundle\Entity\User;

class KhatianApplicationActionMenuManager
{
    /** @var AuthorizationChecker */
    private $authorization;

    /** @var User */
    private $loggedInUser;

    /** @var Router */
    private $router;

    /** @var Khatian */
    private $khatian;

    /** @var KhatianLog */
    private $khatianLog;

    public function __construct(AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage, Router $router)
    {
        $this->authorization = $authorizationChecker;
        $this->router = $router;
        $this->loggedInUser = $tokenStorage->getToken()->getUser();
    }

    /**
     * @param $khatian
     * @return $this
     */
    public function setKhatian($khatian)
    {
        $this->khatian = $khatian;

        return $this;
    }

    /**
     * @param $khatianLog
     * @return $this
     */
    public function setKhatianLog($khatianLog)
    {
        $this->khatianLog = $khatianLog;

        return $this;
    }

    public function render(KhatianLog $khatianLog)
    {
        $this->setKhatianLog($khatianLog);
        $this->setKhatian($khatianLog->getKhatianVersion()->getKhatian());
        $link = $this->getEntryOperatorMenu() . $this->getVerifierMenu() . $this->getComparerMenu() . $this->getApprovalMenu();

        $menu = '';
        if (!empty($link)) {
            $menu .= $link;
        }

        return $menu;
    }

    protected function getEntryOperatorMenu()
    {
        $html = '';

        if (!$this->authorization->isGranted('ROLE_KHATIAN_ENTRY')) {
            return $html;
        }

        if (in_array($this->khatianLog->getKhatianStatus(), array('DRAFT', 'CORRECTION_REQUIRED'))) {
            $html .= '<a href="'.$this->router->generate('entry_operator_khatian_pages', array('id' => $this->khatianLog->getId())).'" class="btn blue btn-sm" title="সম্পাদনা "><i class="fa fa-edit"></i></a>';
            $khatianVersion = $this->khatianLog->getKhatianVersion();
            if ($this->khatianLog->getVerifier() == null && $this->khatianLog->getComparer() == null && $this->khatianLog->getApprover() == null) {
                //$html .= '<a href="'.$this->router->generate('delete_khatian', array('id' => $this->khatian->getId())).'" class="btn red btn-sm">Delete</a>';
            }
        } elseif ($this->khatianLog->getKhatianStatus() == 'READY_FOR_VERIFICATION') {
            $html .= '<a href="'.$this->router->generate('khatian_pages', array('id' => $this->khatianLog->getId())).'" class="btn blue btn-sm" title="দেখুন " ><i class="fa fa-file-o"></i></a>';
            $html .= '<a href="'.$this->router->generate('porcha_request_move_to_draft', array('id' => $this->khatianLog->getId())).'" class="btn blue btn-sm" title="খসড়াতে প্রেরণ "><i class="fa fa-undo"></i></a>';
        }

        if ($this->khatianLog->getKhatianStatus() == 'DRAFT' && $this->khatianLog->getVerifier() == null) {
            $html .= '<a href="'.$this->router->generate('khatian_pages', array('id' => $this->khatianLog->getId())).'" class="btn blue btn-sm" title="দেখুন " ><i class="fa fa-file-o"></i></a>';
            //$html .= '<a href="'.$this->router->generate('delete_khatian', array('id' => $this->khatianLog->getId())).'" class="btn red btn-sm confirm" title="খতিয়ান মুছুন"><i class="fa fa-times"></i></a>';
        }

        return $html;
    }

    protected function getVerifierMenu()
    {
        $html = '';

        if (!$this->authorization->isGranted(array('ROLE_KHATIAN_VERIFICATION'))) {
            return $html;
        }

        $html .= $this->getKhatianViewLink('READY_FOR_VERIFICATION');

        return $html;
    }

    protected function getComparerMenu()
    {   $html = '';

        if (!$this->authorization->isGranted(array('ROLE_KHATIAN_COMPARISON'))) {
            return $html;
        }

        $html .= $this->getKhatianViewLink('READY_FOR_COMPARISON');

        return $html;
    }

    protected function getApprovalMenu()
    {
        $html = '';

        if (!$this->authorization->isGranted(array('ROLE_KHATIAN_APPROVAL'))) {
            return $html;
        }

        $html .= $this->getKhatianViewLink('READY_FOR_APPROVAL');

        return $html;
    }

    private function getKhatianViewLink($status)
    {
        $html = '';

        $lockedBy = $this->khatian->getLockedBy();
        if ($this->khatianLog->getKhatianStatus() == $status ||
            (
                $lockedBy && $lockedBy->getId() == $this->loggedInUser->getId() && $this->khatian->isLocked()
            )
        ) {
            $html .= '<a href="'.$this->router->generate('porcha_request_workflow_khatian_pages', array('id' => $this->khatianLog->getId())).'" class="btn blue btn-sm" title="খাতিয়ান দেখুন "><i class="fa fa-file-o"></i></a>';
        }

        return $html;
    }
}