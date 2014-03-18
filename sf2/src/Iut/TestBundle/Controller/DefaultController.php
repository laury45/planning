<?php

namespace Iut\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/simple")
     * @Template()
     */
    public function simpleAction(){
    	$title="Super titre"; 
    	return array('title'=>$title); 

    }

    /**
    *@Route("/array")
    *@Template()
    */

    public function arrayAction(){
    	$tab = array(1,2,3); 
    	return array('tab'=>$tab); 
    }
}
