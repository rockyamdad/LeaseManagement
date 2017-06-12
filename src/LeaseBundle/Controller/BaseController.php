<?php

namespace LeaseBundle\Controller;


use LeaseBundle\Entity\Application;
use LeaseBundle\Entity\Document;
use LeaseBundle\Entity\Comment;
use LeaseBundle\Entity\PaymentSchedule;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class BaseController extends Controller
{

    /**
     * @param Request $request
     * @param $application
     * @internal param $type
     */
    public function allDocumentUpload(Request $request, $application)
    {

        if (isset($request->files->all()['nid'])) {
            /** @var UploadedFile $file */
            $file = $request->files->all()['nid'];
            $this->uploadDocument($file, $application, $type = 'Nid');
        }
        if (isset($request->files->all()['photo'])) {
            /** @var UploadedFile $file */
            $file = $request->files->all()['photo'];
            $this->uploadDocument($file, $application, $type = 'Photo');
        }
        if (isset($request->files->all()['trade'])) {
            /** @var UploadedFile $file */
            $file = $request->files->all()['trade'];
            $this->uploadDocument($file, $application, $type = 'Trade');
        }
        if (isset($request->files->all()['resulation'])) {
            /** @var UploadedFile $file */
            $file = $request->files->all()['resulation'];
            $this->uploadDocument($file, $application, $type = 'Resulation');
        }
        if (isset($request->files->all()['audit'])) {
            /** @var UploadedFile $file */
            $file = $request->files->all()['audit'];
            $this->uploadDocument($file, $application, $type = 'Audit');
        }
        if (isset($request->files->all()['nibondhon'])) {
            /** @var UploadedFile $file */
            $file = $request->files->all()['nibondhon'];
            $this->uploadDocument($file, $application, $type = 'Nibondhon');
        }
    }
    /**
     * @param $file
     * @param $application
     * @param $type
     */
    protected function uploadDocument($file, $application , $type)
    {
        $document = new Document();
        $uploaded = $file->move($document->getUploadRootDir(), $file->getClientOriginalName());
        if ($uploaded) {
            $document->setRefId($application->getId());
            $document->setEntity('application');
            $document->setPrivacy('Public');
            $document->setType($type);
            $document->setPath($uploaded->getFilename());
        }
        $this->getDoctrine()->getRepository('LeaseBundle:Document')->create($document);
    }

    /**
     * @param Application $application
     * @param $leaseAssign
     * @param $totalAmount
     * @param Request $request
     */
    protected function paymentScheduleCreate(Request $request,Application $application, $leaseAssign, $totalAmount)
    {
        if ($leaseAssign->getApplications()->getLease()->getType() == 'WaterBody' and isset( $request->request->all()['demandFee'])) {
            $chalanAmountPerYear = $totalAmount / 3;
            for ($i = 1; $i < 4; $i++) {
                $paymentSchedule = new PaymentSchedule();
                $paymentSchedule->setAmount($chalanAmountPerYear);
                $startDate = $application->getLease()->getStartDate();
                if ($i == 1) {
                    $paymentSchedule->setPaymentDate($startDate);
                    $paymentSchedule->setRegisterSixes($leaseAssign);
                } elseif ($i == 2) {
                    $division = $leaseAssign->getChalanAmount() / $chalanAmountPerYear;
                    if ($division == 2 or $totalAmount == $leaseAssign->getChalanAmount()) {
                        $paymentSchedule->setRegisterSixes($leaseAssign);
                    }
                    $secondYear = $startDate->add(new \DateInterval('P1Y'));
                    $paymentSchedule->setPaymentDate($secondYear);
                } else {
                    $div = $leaseAssign->getChalanAmount() / $chalanAmountPerYear;
                    if ($div == 3) {
                        $paymentSchedule->setRegisterSixes($leaseAssign);
                    }
                    $thirdYear = $startDate->add(new \DateInterval('P2Y'));
                    $paymentSchedule->setPaymentDate($thirdYear);
                }
                $paymentSchedule->setApplications($application);
                $this->getDoctrine()->getRepository('LeaseBundle:PaymentSchedule')->create($paymentSchedule);
            }

        } else {
            $payments = $this->getDoctrine()->getRepository('LeaseBundle:PaymentSchedule')->findBy(array('registerSixes' => null));
            $chalanAmountPerYear = $application->getTotalAmount() / 3;

            foreach ($payments as $payment) {

                $division = $leaseAssign->getChalanAmount() / $chalanAmountPerYear;
                if ($division == 1) {
                    $payment->setRegisterSixes($leaseAssign);
                    $this->getDoctrine()->getRepository('LeaseBundle:PaymentSchedule')->create($payment);
                    break;
                } elseif ($division == 2) {
                    $payment->setRegisterSixes($leaseAssign);
                    $this->getDoctrine()->getRepository('LeaseBundle:PaymentSchedule')->create($payment);
                }

            }
        }
    }

    /**
     * @return mixed
     */
    protected function createTrackingNo()
    {
        $stamp = date("Yhs");
        $rand = rand(0, 9999);
        $orderid = "$stamp$rand";
        $applicationTrackingId = str_replace(".", "", "$orderid");
        return $applicationTrackingId;
    }
    /**
     * @param Request $request
     * @param $lease
     * @return array
     */
    protected function privateDocumentUpload(Request $request, $entity,  $lease)
    {
        if (isset($request->files->all()['doc_upload1'])) {
            /** @var UploadedFile $file */
            $files = $request->files->all()['doc_upload1'];
            foreach ($files as $file) {
                $document = new Document();
                $uploaded = $file->move($document->getUploadRootDir(), $file->getClientOriginalName());
                if ($uploaded) {
                    $document->setRefId($lease->getId());
                    $document->setEntity($entity);
                    $document->setPrivacy('Private');
                    if($request->request->all()['private_caption']){
                        $document->setType($request->request->all()['private_caption']);
                    }
                    $document->setPath($uploaded->getFilename());
                }
                $this->getDoctrine()->getRepository('LeaseBundle:Document')->create($document);
            }
            return array($files, $file, $document, $uploaded);
        }
    }

    /**
     * @param Request $request
     * @param $lease
     */
    protected function publicDocumentUpload(Request $request, $entity, $lease)
    {
        if (isset($request->files->all()['doc_upload2'])) {
            /** @var UploadedFile $file */
            $files = $request->files->all()['doc_upload2'];
            foreach ($files as $file) {
                $document = new Document();
                $uploaded = $file->move($document->getUploadRootDir(), $file->getClientOriginalName());
                if ($uploaded) {
                    $document->setRefId($lease->getId());
                    $document->setEntity($entity);
                    $document->setPrivacy('Public');
                    if($request->request->all()['public_caption']){
                        $document->setType($request->request->all()['public_caption']);
                    }
                    
                    $document->setPath($uploaded->getFilename());
                }
                $this->getDoctrine()->getRepository('LeaseBundle:Document')->create($document);
            }
        }
    }
    /**
     * @param $application
     */
    protected function commentCreate($application) {

        $type = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUserType($this->getUser());
        $comment = new Comment();
        $comment->setCreatedDate(new \DateTime());
        $comment->setMessage($_POST['comment']);
        $comment->setUsersBy($this->getUser());
        $comment->setUserRole($type['name']);
        $comment->setRefId('APP_'.$application->getId());
        $this->getDoctrine()->getRepository('LeaseBundle:Comment')->create($comment);

    }

    /**
     * @param Request $request
     * @param $leaseAssign
     */
    protected function registerFileUpload(Request $request, $leaseAssign)
    {
        if (isset($request->files->all()['register'])) {
            /** @var UploadedFile $file */
            $files = $request->files->all()['register'];
            foreach ($files as $file) {
                $document = new Document();
                $uploaded = $file->move($document->getUploadRootDir(), $file->getClientOriginalName());
                if ($uploaded) {
                    $document->setRefId($leaseAssign->getId());
                    $document->setEntity('register');
                    if($request->request->all()['register_caption']) {
                        $document->setType($request->request->all()['register_caption']);
                    }
                    $document->setPath($uploaded->getFilename());
                }
                $this->getDoctrine()->getRepository('LeaseBundle:Document')->create($document);
            }
        }
    }
}
