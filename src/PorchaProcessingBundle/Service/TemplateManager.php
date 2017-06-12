<?php
namespace PorchaProcessingBundle\Service;

use AppBundle\Entity\Document;
use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityManager;
use PorchaProcessingBundle\Entity\District;
use PorchaProcessingBundle\Entity\Upozila;
use PorchaProcessingBundle\Entity\OfficeTemplate;
use PorchaProcessingBundle\Entity\Template;

class TemplateManager
{
    use QueryAssistant;
    protected $em;

    public function __construct(EntityManager $entityManager) {
        $this->em = $entityManager;
    }

    public function getTemplateList($data) {

        return $this->em->getRepository('PorchaProcessingBundle:Template')->getTemplateList($data);
    }

    public function getOfficeTemplateList($data, $district = null) {

        return $this->em->getRepository('PorchaProcessingBundle:OfficeTemplate')->getOfficeTemplateList($data, $district);
    }

    public function getOfficeTemplatesByOfficeId($officeId, $surveyType = false)
    {
        if ($surveyType) {
            $qb = $this->em->getRepository('PorchaProcessingBundle:OfficeTemplate')->createQueryBuilder('ot');
            $qb->join('ot.template', 't');
            $qb->join('t.survey', 's');
            $qb->where('s.type = :type')->setParameter('type', $surveyType);
            return $qb->getQuery()->getResult();
        }
        return $this->em->getRepository('PorchaProcessingBundle:OfficeTemplate')->findBy(array('office' => $officeId));
    }

    public function getKhatianEntryTemplates($officeId, $surveyType, $pageType = 'PAGE1')
    {
        $ret = array();
        $page1 = array();
        $page1Additional = array();
        $page2 = array();
        $page2Additional = array();

        $qb = $this->em->getRepository('PorchaProcessingBundle:OfficeTemplate')->createQueryBuilder('ot');
        $qb->join('ot.template', 't');
        $qb->join('t.survey', 's');
        $qb->where('ot.office = :office')->setParameter('office', $officeId);
        $qb->andWhere('s.type = :type')->setParameter('type', $surveyType);
        $rows = $qb->getQuery()->getResult();
        if ($rows) {
            foreach ($rows as $row) {
                switch (strtoupper($row->getTemplate()->getType())) {

                    case 'PAGE1':
                        $page1[$row->getId()] = $row->getTemplate()->getName();
                        $ret['PAGE1'] = $page1;
                        break;
                    case 'PAGE1_ADDITIONAL':
                        $page1Additional[$row->getId()] = $row->getTemplate()->getName();
                        $ret['PAGE1_ADDITIONAL'] = $page1Additional;
                        break;
                    case 'PAGE2':
                        $page2[$row->getId()] = $row->getTemplate()->getName();
                        $ret['PAGE2'] = $page2;
                        break;
                    case 'PAGE2_ADDITIONAL':
                        $page2Additional[$row->getId()] = $row->getTemplate()->getName();
                        $ret['PAGE2_ADDITIONAL'] = $page2Additional;
                        break;
                }
            }
        }
        return $ret;
    }

    public function setToMyOffice(Template $template, $currentUser) {

        $officeTemplate = $this->em->getRepository('PorchaProcessingBundle:OfficeTemplate')->findOneBy(array(
                'office' => $currentUser->getOffice(),
                'template' => $template
            )
        );

        $ret = 'SET';
        if ($officeTemplate) {
            $this->em->remove($officeTemplate);
            $ret = 'UNSET';
        } else {
            $officeTemplate = new OfficeTemplate();
            $officeTemplate->setOffice($currentUser->getOffice());
            $officeTemplate->setTemplate($template);
            $officeTemplate->setSetBy($currentUser);
            $this->em->persist($officeTemplate);
        }
        $this->em->flush();

        return $ret;
    }

    public function getOfficeTemplate(Template $template, $user) {

        return $this->em->getRepository('PorchaProcessingBundle:OfficeTemplate')->findOneBy(array(
                'office' => $user->getOffice(),
                'template' => $template
            )
        );
    }

}