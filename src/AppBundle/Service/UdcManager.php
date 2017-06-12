<?php
namespace AppBundle\Service;

use AppBundle\Entity\CourtFee;
use AppBundle\Entity\DeliveryDaySettings;
use AppBundle\Entity\District;
use AppBundle\Entity\Office;
use AppBundle\Entity\OfficeSettings;
use AppBundle\Entity\Udc;
use AppBundle\Entity\Upozila;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use AppBundle\Entity\SiteMeta;
use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\DateTime;
use UserBundle\Entity\User;

class UdcManager
{
    use QueryAssistant;
    protected $em;
    protected $user;
    protected $office;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage) {

        $this->em = $entityManager;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->office = $this->user->getOffice();
    }

    public function createUdc(Udc $udc){

        $parentOffice = $this->em->getRepository('AppBundle:Office')
            ->findOneBy(array('type'=>'DC','district'=>$udc->getDistrict()));
        $office  = new Office();
//        $user =  $this->udcUserCreate($office,$udc->getUser());
        $office->setParent($parentOffice);
        $office->setActive(false);
        $office->setDistrict($udc->getDistrict());
        $this->em->persist($office);
        $this->em->flush();
        $udc->setStatus('WAITING_FOR_APPROVAL');
        $udc->setOffice($office);
        $udc->setCreatedAt(new \DateTime());

        $this->em
            ->getRepository('AppBundle:Udc')
            ->create($udc);
    }

    /**
     * @param $office
     * @return User
     */
    private function udcUserCreate($office,User $user)
    {
        $user->setEnabled(false);
        $user->setOffice($office);
        $user->setEmail('udc@'.'udc'.$user->getUsername().'.com');
        $this->em->persist($user);
        return $user;
    }

    public function getRelatedDistricts() {

        $relatedDistricts = $this->office->getRelatedDistricts();

        $qb = $this->em->getRepository('AppBundle:District')->createQueryBuilder('d');
        $qb->where('d.approved = 1');
        $qb->andWhere('d.deleted = 0');

        if (!empty($relatedDistricts)) {
            $relatedDistricts = explode(",", $relatedDistricts);
            $qb->andwhere('d.geocode IN (:districtGeocode)');
            $qb->setParameter('districtGeocode', array_values($relatedDistricts));
        }

        return $qb->getQuery()->getResult();
    }

    public function getUdcList($data)
    {
        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('AppBundle:Udc')->createQueryBuilder('u');
        $qb->join('u.upozila', 'up');
        $qb->join('u.district', 'd');
        $qb->join('u.union', 'un');

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }
//        $qb = $this->singleSearchQuery($qb, $params['arrSingleSearchField']);
        $qb = $this->filterQuery($qb, $params['arrFilterField']);
        return $qb->getQuery()->getResult();
    }

    public function getApplicationStatisticsByStatus($udcOffices)
    {
        return $this->em->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestStatisticsByStatus($udcOffices);
    }
    public function getApplicationStatisticsByType($udcOffices)
    {
        return $this->em->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestStatisticsByApplication($udcOffices);
    }


}