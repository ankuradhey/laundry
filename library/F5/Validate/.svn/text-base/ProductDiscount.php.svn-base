<?php

class F5_Validate_ProductDiscount extends Zend_Validate_Abstract
{
    const HIGH_DISCOUNT = 'high_discount';
            
    protected $_messageTemplates = array(
        self::HIGH_DISCOUNT => ''
    );
    
    public function isValid($product_discount, $context = null)
    {   
        switch ($context['product_discount_type']) {
			case 'p':
            
                if(!($context['product_price'] > ($context['product_price'] * $product_discount / 100))) {
                    $this->_error(self::HIGH_DISCOUNT);
                    return false;                	
                }
                
				break;

            case 'f':

                if(!($context['product_price'] > $product_discount)) {
                    $this->_error(self::HIGH_DISCOUNT);
                    return false;                   
                }
                                
                break;
                		
			default:
				break;
		}

        return true;
    }
}
