<?php

class Zend_View_Helper_CartSteps extends Zend_View_Helper_Partial
{
	public function getSteps($currentStep = 1, $orderType='service')
	{
            return $this->partial('index/cartsteps.phtml',array('currentStep'=>$currentStep, 'orderType'=>$orderType));
	}
}