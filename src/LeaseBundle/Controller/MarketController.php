<?php

namespace LeaseBundle\Controller;
use LeaseBundle\Entity\Market;
use LeaseBundle\Form\MarketType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;

class MarketController extends Controller
{
    /**
     * @JMS\Secure(roles="ROLE_MENU_ITEM_MARKET_CREATE")
     */
    public function marketCreateAction(Request $request)
    {
        $market = new Market();
        $form = $this->createForm(new MarketType(), $market);

        if($request->getMethod()=="POST")
        {
            $form->handleRequest($request);
            if($form->isValid()) {
                $this->addFlash('success', 'মার্কেট সফলভাবে তৈরি করা হয়েছে');
                $this->getDoctrine()->getRepository('LeaseBundle:Market')->create($market);

                return $this->redirectToRoute('market_list');
            }
        }
        return $this->render('LeaseBundle:Market:create.html.twig',array(
            'form' =>$form->createView(),
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_MENU_ITEM_MARKET_LIST")
     */
    public function marketListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $allMarkets = $this->getDoctrine()->getRepository('LeaseBundle:Market')->getAllMarketQueryBuilder();
        $markets = $this->get('knp_paginator')->paginate($allMarkets, $page, $perPage, array('sort' => 'm.id', 'direction'=>'DESC'));

        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Market:market_list_partial.html.twig',array(
                'markets'=>$markets
            ));
        }
        else{
            return $this->render('LeaseBundle:Market:market_list.html.twig',array(
                'markets'=>$markets
            ));
        }
        
    }
    /**
     * @JMS\Secure(roles="ROLE_MENU_ITEM_MARKET_EDIT")
     */
    public function marketEditAction(Request $request,$id)
    {
        $market= $this->getDoctrine()->getRepository('LeaseBundle:Market')->find($id);
        $form = $this->createForm(new MarketType(),$market);

        if($request->getMethod()=='POST'){
            $form->handleRequest($request);
            if($form->isValid()){
                $this->addFlash('success', 'মার্কেট সফলভাবে আপডেট করা হয়েছে');
                $this->getDoctrine()->getRepository('LeaseBundle:Market')->create($market);

                
                return $this->redirectToRoute('market_list');
            }
        }
        return $this->render('LeaseBundle:Market:update.html.twig',array(
            'form'=>$form->createView(),
        ));
        
    }

}
