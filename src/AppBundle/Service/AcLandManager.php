<?php
namespace AppBundle\Service;

use AppBundle\Entity\ACLandDocument;
use AppBundle\Entity\AdditionalFee;
use AppBundle\Entity\CourtFee;
use AppBundle\Entity\DeliveryDaySettings;
use AppBundle\Entity\Office;
use AppBundle\Entity\Holiday;
use AppBundle\Entity\OfficeSettings;
use AppBundle\Entity\SmsSetting;
use PorchaProcessingBundle\Entity\PorchaCopyRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use AppBundle\Entity\SiteMeta;
use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityManager;

class AcLandManager
{
    use QueryAssistant;
    protected $em;
    protected $user;

    /** @var Office */
    protected $office;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage) {

        $this->em = $entityManager;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->office = $this->user->getOffice();
    }
    
    public function saveCopyRequestAcLand(PorchaCopyRequest $copyRequest) {        
        $this->saveCopyRequest($copyRequest, $this->office->getParent());
    }
    
    public function saveCopyRequestDc(PorchaCopyRequest $copyRequest) {
        $this->saveCopyRequest($copyRequest, false);
    }
    public function replyCopyRequest(PorchaCopyRequest $copyRequest) {

        $copyRequest->setRepliedSeen(true);
        $copyRequest->setStatus('REPLIED');
        $this->em->persist($copyRequest);
        $this->em->flush();
    }

    private function saveCopyRequest(PorchaCopyRequest $copyRequest , $toOffice) {
        
        if($toOffice){
            $copyRequest->setToOffice($toOffice);
        }
        $copyRequest->setCreatedAt(new \DateTime());
        $copyRequest->setCreatedBy($this->user);
        $copyRequest->setStatus('REPLIED_NEEDED');
        $copyRequest->setOffice($this->office);

        $this->em->persist($copyRequest);
        $this->em->flush();
    }

    public function saveAcLandDocument($files,$copyRequest){

        /** @var UploadedFile $file */
        foreach ($files as $file) {

            $acLandDocument = new ACLandDocument();
            $acLandDocument->setFile($file);
            $acLandDocument->upload();
            $acLandDocument->setFileType($file->getClientMimeType());
            $acLandDocument->setCreatedBy($this->user);
            $acLandDocument->setFileName($file->getClientOriginalName());
            
            $acLandDocument->setPorchaCopyRequest($copyRequest);
            $this->em->persist($acLandDocument);

        }
    }

    public function viewableCopyRequest (PorchaCopyRequest $copyRequest) {

        $copyRequest->setRequestSeen(true);

        $this->em->persist($copyRequest);
        $this->em->flush();
    }


}