<?php

class Application_Form_CouponForm extends Zend_Form {

    public $elementDecorators = array(
        'ViewHelper',
        'Errors',
        array('Description', array('tag' => 'p', 'class' => 'description')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'mws-form-item large')),
        array('Label', array('class' => 'control-label', 'requiredSuffix' => ' *')),
        array('Errors', array('class' => 'zend-error'))
    );

    public function init() {
        /* Form Elements & Other Definitions Here ... */
        $this->setMethod('post');
        $this->setElementDecorators($this->elementDecorators);

        $this->addElement('select', 'coupon_type', array(
            'label' => 'Coupon Type',
            'required' => true,
            'multiOptions' => array(
                '' => '----- Select Coupon Type -----',
                'flat' => 'Flat',
                'percentage' => 'Percentage',
            ),
            'filters' => array('StringTrim'),
            'class' => 'mws-textinput',
            'validators' => array(
                array('NotEmpty', true, array('messages' => 'Coupon Type is required')),
            ),
        ));

        $this->addElement('text', 'coupon_code', array(
            'label' => 'Coupon Code',
            'required' => true,
            'placeholder' => "Enter Coupon Code",
            'filters' => array('StringTrim'),
            'class' => 'mws-textinput',
        ));

        $this->addElement('text', 'coupon_value', array(
            'label' => 'Coupon Value',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Enter Coupon Value",
            'class' => 'mws-textinput',
        ));
				
		$this->addElement('text', 'coupon_last_date', array(
            'label' => 'Coupon Valid till',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Select Coupon valid last date",
            'class' => 'mws-datepicker mws-textinput',
        ));
		
		$this->addElement('text', 'coupon_occourence', array(
            'label' => 'Coupon Occurence',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Enter coupon used by single user",
            'class' => 'mws-textinput',
        ));
		
		$this->addElement('text', 'coupon_min_billing', array(
            'label' => 'Minimum Billing required',
            'required' => false,
            'filters' => array('StringTrim'),
            'placeholder' => "Min billing required to use this coupon",
            'class' => 'mws-textinput',
        ));
		
		$this->addElement('text', 'coupon_max_discount', array(
            'label' => 'Max Discount',
            'required' => false,
            'filters' => array('StringTrim'),
            'placeholder' => "Max discount",
            'class' => 'mws-textinput',
        ));
		
		$this->addElement('select', 'coupon_status', array(
            'label' => 'Coupon Status',
            'required' => true,
            'multiOptions' => array(               
                '1' => 'Active',
                '0' => 'In-Active',
            ),
            'filters' => array('StringTrim'),
            'class' => 'mws-textinput',
            'validators' => array(
                array('NotEmpty', true, array('messages' => 'Coupon Type is required')),
            ),
        ));
		
    }

}
