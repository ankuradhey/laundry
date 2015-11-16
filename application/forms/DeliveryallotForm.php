<?php 

class Application_Form_DeliveryallotForm extends Zend_Form {

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

        $ordersMapper = new Application_Model_OrdersMapper();
        $orders = $ordersMapper->getOrdersByStatus();
        
        $order_options = array('Select Order');
        
        if($orders){
            foreach($orders as $order){
                $order_options[$order->__get("order_id")] = $order->__get("order_id");
            }
        }
        
        $delboyMapper = new Application_Model_DeliveryBoyMapper();
        $delboys = $delboyMapper->getAllDeliveryBoys();
        
        $delboy_options = array('Select Delivery Boy');
        if($delboys){
            foreach($delboys as $delboy){
                $delboy_options[$delboy->__get("delboy_id")] = $delboy->__get("delboy_fname")." ".$delboy->__get('delboy_lname');
            }
        }
        
        
        $this->addElement('select', 'orders', array(
            'label' => 'Order',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Select Order",
            'class' => 'mws-textinput',
            'multiOptions' => $order_options
        ));
        
        
        $this->addElement('select', 'delboy_id', array(
            'label' => 'Choose Delivery Boy',
            'required' => true,
            'filters' => array('StringTrim'),
            'placeholder' => "Choose Delivery boy",
            'class' => 'mws-textinput',
            'multiOptions' => $delboy_options
        ));
        
    }

}
