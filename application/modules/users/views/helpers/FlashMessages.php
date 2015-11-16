<?php
class Zend_View_Helper_FlashMessages extends Zend_View_Helper_Abstract
{
	public function flashMessages()
	{
		$messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
		$output = '';
		if (!empty($messages)) {
			//$output .= '<ul>';
			foreach ($messages as $message) {
				$output .= '<li class="' . key($message) . '">' . current($message) . '</li>';
			}
			//$output .= '</ul>';
		}
		return $output;
	}
}
?>