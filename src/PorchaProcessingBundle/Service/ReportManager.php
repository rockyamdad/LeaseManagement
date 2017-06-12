<?php
namespace PorchaProcessingBundle\Service;

use AppBundle\Traits\QueryAssistant;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\KhatianVersion;
use PorchaProcessingBundle\Generator\IdGenerator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use PorchaProcessingBundle\Entity\Volume;
use Symfony\Component\HttpFoundation\Session\Session;

class ReportManager
{
    use QueryAssistant;
    protected $em;
    protected $user;
    protected $office;
    protected $offices;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage) {

        $this->em = $entityManager;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->office = $this->user->getOffice();

        $this->offices = array($this->office->getId());
        if ($this->user->getOffice()->getChildren()) {
            foreach ($this->user->getOffice()->getChildren() as $children) {
                $this->offices[] = $children->getId();
            }
        }
    }

    public function getServiceRequestReportList($data, $reportType = 'RECEIVED') {

        if(empty($data)){
            return array();
        }
        $params = $this->queryParameters($data);

        $serviceType = $params['arrFilterField']['sr.type'];

        switch($serviceType) {

            case 'PORCHA_REQUEST':
            case 'MOUZA_MAP':
                   $qb =  $this->getServicePorchaRequest();
                   break;
            case 'INFORMATION_SLIP':
                 $qb = $this->getServiceInformationSlip();
                 break;
            case 'CASE_COPY':
                $qb = $this->getServiceCaseCopy();
                break;
        }

        $qb->where('sr.office = :office')->setParameter('office', $this->office);

        if($reportType == 'RECEIVED') {

            $qb->andWhere('sr.status != :status')->setParameter('status', 'DELIVERED');

        } elseif($reportType == 'DELIVERED') {

            $qb->andWhere('sr.status = :status')->setParameter('status', 'DELIVERED');
        }

        if(!empty($params['arrRangeFilterField1']['sr.createdAt'])){
            $params['arrRangeFilterField1']['sr.createdAt'] = date('Y-m-d',strtotime($params['arrRangeFilterField1']['sr.createdAt']));
        }
        if(!empty($params['arrRangeFilterField2']['sr.createdAt'])) {
            $params['arrRangeFilterField2']['sr.createdAt'] = date(
                'Y-m-d',
                strtotime($params['arrRangeFilterField2']['sr.createdAt'])
            );

        }

        if($serviceType !='PORCHA_REQUEST' and $serviceType != 'MOUZA_MAP'){
            $params['arrFilterField']['p.district'] = '';
            $params['arrFilterField']['p.upozila'] = '';
            $params['arrFilterField']['p.mouza'] = '';
            $qb = $this->filterQuery($qb, $params['arrFilterField']);
        }else{
            $qb = $this->filterQuery($qb, $params['arrFilterField']);
        }



        $qb = $this->rangeFilterQuery($qb, $params['arrRangeFilterField1'], $params['arrRangeFilterField2']);

        return $qb->getQuery()->getResult();
   }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getServicePorchaRequest()
    {
        $qb = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->createQueryBuilder('p');
        $qb->join('p.serviceRequest', 'sr');
        $qb->leftJoin('sr.office', 'o');
        $qb->leftJoin('p.survey', 's');
        $qb->leftJoin('p.upozila', 'u');
        $qb->leftJoin('p.mouza', 'm');
        return $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getServiceInformationSlip()
    {
        $qb = $this->em->getRepository('PorchaProcessingBundle:ServiceRequest')->createQueryBuilder('sr');

        return $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getServiceCaseCopy()
    {
        $qb = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestCaseCopy')->createQueryBuilder('sc');

        $qb->leftJoin('sc.serviceRequest', 'sr');

        return $qb;
    }

    public function getVolumeWiseReportList($requestAll)
    {
        if(empty($requestAll)){
            return array();
        }
        $params = $this->queryParameters($requestAll);

        $qb = $this->em->getRepository('PorchaProcessingBundle:KhatianLog')->createQueryBuilder('kl');
        $qb->innerJoin('kl.khatianVersion', 'kv');
        $qb->innerJoin('kv.khatian', 'k');
        $qb->innerJoin('k.volume', 'v');
    /*    $qb->innerJoin('v.volumeMouzas', 'vm');
        $qb->innerJoin('vm.mouza', 'm');*/

        $qb->where('k.office = :office');
        $qb->setParameter('office', $this->office);
        $this->filterQuery($qb, $params['arrFilterField']);
        $qb->andWhere("kl.khatianStatus NOT IN ('NONE')");
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('kl.batch', true),
            $qb->expr()->eq('kl.firstApp', true)
        ));

        return $qb->getQuery()->getResult();


    }

    public function getDeliveryRegisterReportList($requestAll,$serviceType)
    {
        if(empty($requestAll)){
            return array();
        }
        $params = $this->queryParameters($requestAll);

        $qb = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->createQueryBuilder('pr');
        $qb->innerJoin('pr.district', 'd');
        $qb->innerJoin('pr.upozila', 'u');
        $qb->innerJoin('pr.mouza', 'm');
        $qb->innerJoin('pr.serviceRequest', 'sr');
        $qb->innerJoin('pr.survey','s');
        $qb->where('sr.office = :office');
        $qb->setParameter('office', $this->office);

        if($serviceType == 'PORCHA_REQUEST') {

            $qb->andWhere('sr.type = :type');
            $qb->setParameter('type', 'PORCHA_REQUEST');

        } elseif($serviceType == 'MOUZA_MAP'){

            $qb->andWhere('sr.type = :type');
            $qb->setParameter('type', 'MOUZA_MAP');
        }

        if(!empty($params['arrRangeFilterField1']['sr.createdAt'])){
            $params['arrRangeFilterField1']['sr.createdAt'] = date(
                'Y-m-d',strtotime($params['arrRangeFilterField1']['sr.createdAt']));
        }
        if(!empty($params['arrRangeFilterField2']['sr.createdAt'])) {
            $params['arrRangeFilterField2']['sr.createdAt'] = date(
                'Y-m-d',
                strtotime($params['arrRangeFilterField2']['sr.createdAt'])
            );

        }

        $qb = $this->rangeFilterQuery($qb, $params['arrRangeFilterField1'], $params['arrRangeFilterField2']);

        $qb = $this->filterQuery($qb, $params['arrFilterField']);

        return $qb->getQuery()->getResult();

    }
    public function getCourtFeeRegisterReportList($requestAll)
    {
        if(empty($requestAll)){
            return array();
        }
        $params = $this->queryParameters($requestAll);

        $qb = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->createQueryBuilder('pr');
        $qb->innerJoin('pr.district', 'd');
        $qb->innerJoin('pr.upozila', 'u');
        $qb->innerJoin('pr.mouza', 'm');
        $qb->innerJoin('pr.serviceRequest', 'sr');
        $qb->where('sr.office = :office');
        $qb->setParameter('office', $this->office);

        if(!empty($params['arrRangeFilterField1']['sr.createdAt'])){
            $params['arrRangeFilterField1']['sr.createdAt'] = date('Y-m-d',strtotime($params['arrRangeFilterField1']['sr.createdAt']));
        }
        if(!empty($params['arrRangeFilterField2']['sr.createdAt'])) {
            $params['arrRangeFilterField2']['sr.createdAt'] = date(
                'Y-m-d',
                strtotime($params['arrRangeFilterField2']['sr.createdAt'])
            );

        }
        $qb = $this->rangeFilterQuery($qb, $params['arrRangeFilterField1'], $params['arrRangeFilterField2']);

        $qb = $this->filterQuery($qb, $params['arrFilterField']);

        return $qb->getQuery()->getResult();

    }

}