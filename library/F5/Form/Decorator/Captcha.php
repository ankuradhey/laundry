<?php

class F5_Form_Decorator_Captcha extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this->getElement();

        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }

        if (null === $element->getView()) {
            return $content;
        } 

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();

        switch ($placement) {
            case (self::PREPEND):
                return '<tr>' . $separator . $content . '</tr>';
            case (self::APPEND):
            default:
                return '<tr>' . $content . $separator . '</tr>';
        }
    }
}