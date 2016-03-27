<?php

class Application_Form_OrdersForm extends Zend_Form {

    public $elementDecorators = array(
        'ViewHelper',
        'Errors',
        array('Description', array('tag' => 'p', 'class' => 'description')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls mws-form-item medium')),
        array('Label', array('class' => 'control-label', 'requiredSuffix' => '*')),
        array('Errors', array('class' => 'zend-error'))
    );

    public function init() {
        /* Form Elements & Other Definitions Here ... */
        $this->setMethod('post');
        $this->setElementDecorators($this->elementDecorators);
        
        //get delivery types from delivery master
        $deliveryMapper = new Application_Model_DeliveryTypeMasterMapper();
        //get service types list
        $serviceMaster = new Application_Model_ServiceMasterMapper();
        $packageMaster = new Application_Model_PackagesMapper();
                
        $this->addElement('text', 'order_mobile_number', array(
            'label' => 'User\'s Phone Number',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Enter User's Contact number",
            'class' => 'mws-textinput required',
        ));
        
        $this->addElement('hidden', 'order_user_id', array(
        ));
        
        
        
        $this->addElement('text', 'order_first_name', array(
            'label' => 'User\'s First Name',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Enter User's First Name",
            'class' => 'mws-textinput required',
        ));
        
        $this->addElement('text', 'order_last_name', array(
            'label' => 'User\'s Last Name',
            'filters' => array('StringTrim'),
            'placeholder' => "Enter User's Last Name",
            'class' => 'mws-textinput',
        ));
        
        $this->addElement('text', 'order_user_email', array(
            'label' => 'User\'s Email',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Enter User's Email",
            'class' => 'mws-textinput',
        ));
        
        $this->addElement('text', 'order_address', array(
            'label' => 'User\'s House/Apartment #',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Enter User's House/Apartment #",
            'class' => 'mws-textinput',
        ));
        
        $this->addElement('text', 'order_city', array(
            'label' => 'City',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Enter City",
            'class' => 'mws-textinput',
        ));
        
//        $this->addElement('text', 'order_pincode', array(
//            'label' => 'Pincode',
//            'filters' => array('StringTrim'),
//            'placeholder' => "Enter Pincode",
//            'class' => 'mws-textinput',
//        ));
        
        $orderType = array('service'=>'service','package'=>'package');
        $this->addElement('select', 'order_type', array(
            'label' => 'Order Type',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Select Order type",
            'class' => 'mws-textinput',
            'multiOptions' => $orderType,
            'onchange'=>'changeOrderType(this.value)'
        ));
        
        
        //============== Service Type
        //get service list
        $serviceList = $serviceMaster->getAllServiceMasters();
        foreach($serviceList as $serviceVal){
            $serviceType[$serviceVal->service_id] = $serviceVal->service_name;
        }
//        $serviceType = array(3=>"laundry",4=>"dryclean",5=>"ironing",6=>"washandfold",0=>"other");
        $this->addElement('multiselect', 'order_service_type', array(
            'label' => 'Service Type',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Select Service Type",
            'class' => 'mws-textinput',
            'multiOptions' => $serviceType,
            'onchange'=>'updateDeliveryType(this.value)'
        ));
        
        
        
        //=========== Delivery Type
        $deliveryTypes = $deliveryMapper->getAllDeliveryTypeMaster();
        
        foreach($deliveryTypes as $delVal){
            $delivery_types[$delVal->delivery_type_id] = $delVal->delivery_type_name;
        }
//        $delivery_types = array("Regular", "Express");
            
        $this->addElement('select', 'order_delivery_type', array(
            'label' => 'Delivery Type',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Select Delivery type",
            'class' => 'mws-textinput',
            'multiOptions' => $delivery_types
        ));

        
        $packageList = $packageMaster->getAllPackages();
        $packageArr = array();
        foreach($packageList as $packageVal){
            $packageArr[$packageVal->package_id] = $packageVal->package_name;
        }
//        $delivery_types = array("Regular", "Express");
            
        $this->addElement('select', 'order_package', array(
            'label' => 'Package',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Select Package",
            'class' => 'mws-textinput',
            'multiOptions' => $packageArr
        ));
        
        
//        $payment_methods = array("Credit Card", "Debit Card", "Net Banking", "Cash On Delivery", "Wallet");
//        $this->addElement('select', 'order_payment_type', array(
//            'label' => 'Payment Method',
//            'required' => true,
//            'filters' => array('StringTrim'),
//            'placeholder' => "Select Payment Method",
//            'class' => 'mws-textinput',
//            'multiOptions' => $payment_methods
//        ));
        
//        $payment_statuses = array("Paid","Unpaid");
//        $this->addElement('select', 'order_payment_status', array(
//            'label' => 'Payment Status',
//            'required' => true,
//            'filters' => array('StringTrim'),
//            'placeholder' => "Select Payment Status",
//            'class' => 'mws-textinput',
//            'multiOptions' => $payment_statuses
//        ));
        
//        $order_statuses = array("New","Picked","Processed","Dispatched","Delivered");
//        $this->addElement('select', 'order_status', array(
//            'label' => 'Order Status',
//            'required' => true,
//            'filters' => array('StringTrim'),
//            'placeholder' => "Select Order Status",
//            'class' => 'mws-textinput',
//            'multiOptions' => $order_statuses
//        ));
        
//        // TO DO - from db
//        $serviceType = array(3=>"laundry",4=>"dryclean",5=>"ironing",6=>"washandfold",0=>"other");
//        $this->addElement('select', 'order_service_type', array(
//            'label' => 'Service Type',
//            'required' => true,
//            'filters' => array('StringTrim'),
//            'placeholder' => "Select Service Type",
//            'class' => 'mws-textinput',
//            'multiOptions' => $order_statuses
//        ));
        
//        $this->addElement('text', 'order_amount', array(
//            'label' => 'Total Price',
//            'required' => true,
//            'filters' => array('StringTrim'),
//            'placeholder' => "Enter Total Price",
//            'class' => 'mws-textinput',
//        ));
    }

}
