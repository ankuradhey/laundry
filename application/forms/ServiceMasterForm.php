<?php

class Application_Form_ServiceMasterForm extends Zend_Form {

    public $elementDecorators = array(
        'ViewHelper',
        'Errors',
        array('Description', array('tag' => 'p', 'class' => 'description')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
        array('Label', array('class' => 'control-label', 'requiredSuffix' => '*')),
        array('Errors', array('class' => 'zend-error'))
    );

    public function init() {
        /* Form Elements & Other Definitions Here ... */
        $this->setMethod('post');
        $this->setElementDecorators($this->elementDecorators);

        $this->addElement('text', 'service_name', array(
            'label' => 'Service Name',
            'required' => true,
            'placeholder' => "Enter Service Name",
            'filters' => array('StringTrim'),
            'class' => 'mws-textinput',
        ));
    }

}
