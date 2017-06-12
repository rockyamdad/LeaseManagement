<?php
namespace PorchaProcessingBundle\Service;

use AppBundle\Entity\Office;
use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityManager;
use EasyBanglaDate\Types\BnDateTime;
use PorchaProcessingBundle\Entity\Report\EntryStatistics;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class DashboardManager
{
    use QueryAssistant;
    protected $em;
    protected $user;
    protected $office;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var KhatianManager */
    protected $khatianManager;

    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    public function __construct(EntityManager $entityManager, TranslatorInterface $translator, KhatianManager $khatianManager, TokenStorage $tokenStorage, AuthorizationCheckerInterface $authorizationCheckerInterface)
    {
        $this->em         = $entityManager;
        $this->translator = $translator;
        $this->khatianManager = $khatianManager;
        $this->authorizationChecker = $authorizationCheckerInterface;

        if ($tokenStorage->getToken()) {
            $this->user   = $tokenStorage->getToken()->getUser();
            $this->office = $this->user->getOffice();
        }
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    private function getReportRowSkeleton()
    {
        $defaultValue = array('complete' => 0, 'correction' => 0);
        $rowDefault = array('today' => $defaultValue, 'yesterday' => $defaultValue, 'total' => $defaultValue);
        return array('ENTRY' => $rowDefault, 'VERIFY' => $rowDefault, 'COMPARE' => $rowDefault, 'APPROVE' => $rowDefault);
    }

    public function getEntryStatistics($date = array('start' => null, 'end' => null), Office $office = null)
    {
        $qb = $this->em->getRepository('PorchaProcessingBundle:Report\EntryStatistics')->createQueryBuilder('r');
        $qb->select('r');
        $qb->orderBy('r.date', 'DESC');

        if ($office) {
            $qb->andWhere('r.office = :office')->setParameter('office', $office->getId());
        }

        if (!empty($date['start'])) {
            $qb->andWhere('r.date >= :start')->setParameter('start', $date['start']);
        }

        if (!empty($date['end'])) {
            $qb->andWhere('r.date <= :end')->setParameter('end', $date['end']);

        }

        $r = $qb->getQuery()->getResult();

        return $r;
    }

    public function dashboardDataStatusWiseSummary($date = null, Office $office = null)
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $endDate = new \DateTime($date);
        $yesterday = (new \DateTime($date))->modify('-1 day')->format('Y-m-d');
        $startDate = (new \DateTime($date))->modify('-6 Days');

        $data = $this->getReportRowSkeleton();
        // Get Data Of last 7 Days
        $lastWeekData = $this->getEntryStatistics(array('start' => $startDate->format('Y-m-d'), 'end' => $endDate->format('Y-m-d')), $office);

        // Prepare Today/Yesterday entry data from Last 7 days data
        /** @var EntryStatistics $row */
        foreach ($lastWeekData as $row) {
            if ($row->getDate()->format('Y-m-d') == $yesterday) {
                $data['ENTRY']['yesterday'] = array('complete' => $row->getEntry(), 'correction' => $row->getEntryCorrection());
                $data['VERIFY']['yesterday'] = array('complete' => $row->getVerify(), 'correction' => $row->getVerifyCorrection());
                $data['COMPARE']['yesterday'] = array('complete' => $row->getCompare(), 'correction' => $row->getCompareCorrection());
                $data['APPROVE']['yesterday'] = array('complete' => $row->getApprove(), 'correction' => $row->getApproveCorrection());
            }

            if ($row->getDate()->format('Y-m-d') == $date) {
                $data['ENTRY']['today'] = array('complete' => $row->getEntry(), 'correction' => $row->getEntryCorrection());
                $data['VERIFY']['today'] = array('complete' => $row->getVerify(), 'correction' => $row->getVerifyCorrection());
                $data['COMPARE']['today'] = array('complete' => $row->getCompare(), 'correction' => $row->getCompareCorrection());
                $data['APPROVE']['today'] = array('complete' => $row->getApprove(), 'correction' => $row->getApproveCorrection());

                $data['ENTRY']['total'] = array('complete' => $row->getEntryTotal(), 'correction' => $row->getEntryTotalCorrection());
                $data['VERIFY']['total'] = array('complete' => $row->getVerifyTotal(), 'correction' => $row->getVerifyTotalCorrection());
                $data['COMPARE']['total'] = array('complete' => $row->getCompareTotal(), 'correction' => $row->getCompareTotalCorrection());
                $data['APPROVE']['total'] = array( 'complete' => $row->getApproveTotal(), 'correction' => $row->getApproveTotalCorrection());
            }
        }

        return array(
            'table' => $data,
            'chart' => $this->prepareChartData($lastWeekData),
        );
    }

    private function prepareChartData($data)
    {
        $rowDefault = array('name' => ' ', 'data' => array());

        $categories = array();
        $series = array('ENTRY' => $rowDefault, 'VERIFY' => $rowDefault, 'COMPARE' => $rowDefault, 'APPROVE' => $rowDefault);
        /** @var EntryStatistics $row */
        foreach ($data as $row) {

            $categories[] = (new BnDateTime($row->getDate()->format('d M y')))->getDateTime()->format('jS F');

            $series['ENTRY']['name'] = $this->translator->trans('entry');
            $series['ENTRY']['data'][] = $row->getEntry();

            $series['VERIFY']['name'] = $this->translator->trans('verify');
            $series['VERIFY']['data'][] = $row->getVerify();

            $series['COMPARE']['name'] = $this->translator->trans('compare');
            $series['COMPARE']['data'][] = $row->getCompare();

            $series['APPROVE']['name'] = $this->translator->trans('approve');
            $series['APPROVE']['data'][] = $row->getApprove();
        }
        $categories = array_values(array_unique($categories));

        return array(
            'categories' => $categories,
            'series' => array_values($series),
        );
    }

    private function getOperatorReportRowSkeleton($type = 'table')
    {
        $defaultValue = array('complete' => 0, 'correction' => 0);
        $rowDefault = array('today' => $defaultValue, 'yesterday' => $defaultValue, 'total' => $defaultValue);

        $data = array();
        foreach ($this->em->getRepository('PorchaProcessingBundle:Survey')->findAll() as $survey) {
            $data['table'][$survey->getType()] = array(
                'label' => $survey->getName(),
                'values' => $rowDefault,
            );

            $rowDefault2 = array('name' => '', 'data' => array());
            $data['chart'][$survey->getType()] = $rowDefault2;
        }

        return $data[$type];
    }

    public function dashboardOperatorData($user, $date = null)
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $endDate = new \DateTime($date);
        $yesterday = (new \DateTime($date))->modify('-1 day')->format('Y-m-d');
        $startDate = (new \DateTime($date))->modify('-7 Days');
        $data = $this->getOperatorReportRowSkeleton();

        $compareField = array('dateField' => 'entryAt', 'userField' => 'entryOperator');
        if ($this->authorizationChecker->isGranted('ROLE_KHATIAN_VERIFICATION')) {
            $compareField = array('dateField' => 'verifiedAt', 'userField' => 'verifier');
        } elseif ($this->authorizationChecker->isGranted('ROLE_KHATIAN_COMPARISON')) {
            $compareField = array('dateField' => 'comparedAt', 'userField' => 'comparer');
        }

        $lastWeekData = $this->em->getRepository('PorchaProcessingBundle:KhatianVersion')->countByKhatianType($user, $compareField, $startDate, $endDate);

        foreach ($lastWeekData as $key => $result) {
            foreach ($result as $row) {

                $rowDate = implode('-', array($row['gYear'], $row['gMonth'], $row['gDay']));

                if ($rowDate == $yesterday) {
                    $data[$row['type']]['values']['yesterday'][$key] = (int)$row['found'];
                }

                if ($rowDate == $date) {
                    $data[$row['type']]['values']['today'][$key] = (int)$row['found'];
                }
            }
        }

        return array(
            'table' => $data,
            'chart' => $this->prepareOperatorChartData($lastWeekData),
        );
    }

    private function prepareOperatorChartData($data)
    {
        /** TODO: Prepare Chart for Operator Dashboard */
        return array();
        $rowDefault = array('name' => '', 'data' => array());

        $categories = array();
        $series = $this->getOperatorReportRowSkeleton('chart');
        $grouping = array();

        $yesterday = (new \DateTime($date))->modify('-1 day')->format('Y-m-d');
        $startDate = (new \DateTime($date))->modify('-7 Days');


        foreach ($data as $key => $result) {
            foreach ($result as $row) {

                $rowDate = implode('-', array($row['gYear'], $row['gMonth'], $row['gDay']));

                if ($rowDate == $yesterday) {
                    $data[$row['type']]['values']['yesterday'][$key] = (int)$row['found'];
                }

                if ($rowDate == $date) {
                    $data[$row['type']]['values']['today'][$key] = (int)$row['found'];
                }

                $data[$row['type']]['values']['total'][$key] += (int)$row['found'];
            }
        }

        foreach ($data as $row) {
            $categories[] = (new BnDateTime($row['date']->format('d M y')))->getDateTime()->format('jS F');

            $series[$row['type']]['name'] = $this->translator->trans($row['type']);
            $series[$row['type']]['data'][] = (int)$row['complete'];
        }
        $categories = array_values(array_unique($categories));

        return array(
            'categories' => $categories,
            'series' => array_values($series),
        );
    }
}