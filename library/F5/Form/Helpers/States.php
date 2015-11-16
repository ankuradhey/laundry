<?php

class F5_Form_Helpers_States extends F5_Db_Table_Abstract
{
    protected $_name = 'default_states';
    protected $_primary = 'state_id';

    protected $_referenceMap = array(
        'Countries' => array(
            'columns'        => array('country_id'),
            'refTableClass'  => 'F5_Form_Helpers_Countries',
            'refColumns'     => array('country_id'),
            'onDelete'       => self::CASCADE,
            'onUpdate'       => self::RESTRICT
        )
    );
            
    public $states = array();

    public function getStates($where = null, $order_by = null)
    {
        $countries = $this->fetchAll(
                            $this->select()
                                 ->order(array($order_by ? $order_by : 'state_code'))
                                 ->where($where ? $where : 1)
        );
        
        if($countries) {            
            foreach ($countries->toArray() as $key => $country) {
				$this->countries[$country['country_code']] = $country['country_name'];
			}
        }
 
        return $this;        
    }
}
    
?>