<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataSourceController extends Controller
{
    public function upozilaListAction($districtId)
    {
        $upozilas = $this->getDoctrine()->getRepository('AppBundle:Upozila')->findBy(array('district' => $districtId, 'approved' => 1, 'deleted' => 0));
        $ret = [];
        foreach ($upozilas as $upozila) {
            $ret[] = ['id' => $upozila->getId(), 'text' => $upozila->getName() ];
        }

        return new JsonResponse($ret);
    }
}
