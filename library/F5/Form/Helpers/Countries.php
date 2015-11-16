<?php

class F5_Form_Helpers_Countries extends F5_Db_Table_Abstract
{
    protected $_name = 'default_countries';
    protected $_primary = 'country_id';

    protected $_dependentTables = array(
        'F5_Form_Helpers_States'
    );

    protected $_row;
    
    public $countries = array();
    public $states = array();
    public $country_id = null;

    public function getCountryId($country_id = null)
    {
        if($country_id) {
            $this->_row = is_numeric($country_id) ? $this->find((int) $country_id)->current() : $this->fetchRow($this->select()->where('country_code = ?', (string) $country_id));
            $this->country_id = $this->_row ? (int) $this->_row->country_id : FALSE;
        }
                               
        return $this;
    }
    
    public function getCountries($where = null, $order_by = null)
    {
        $countries = $this->fetchAll(
                            $this->select()
                                 ->order(array($order_by ? $order_by : 'country_code'))
                                 ->where($where ? $where : 1)
        );
        
        if($countries) {            
            foreach ($countries->toArray() as $key => $country) {
				$this->countries[$country['country_code']] = $country['country_name'];
			}
        }
 
        return $this;        
    }

    public function getStates($country = null, $where = null, $order = null)
    {
        if($country) {
            $this->getCountryId($country);
        }
        
        if($this->_row) {               
            if($states = $this->_row->findDependentRowset('F5_Form_Helpers_States', 'Countries',
                    $this->select()->where($where ? $where : 1)->order(array($order ? $order : 'state_code')))) {
                        
                foreach ($states->toArray() as $key => $state) {
                    $this->states[$state['state_code']] = $state['state_name'];
                }
            }
        }

        return $this;
    }
}
    
?>