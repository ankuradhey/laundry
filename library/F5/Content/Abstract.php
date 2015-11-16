<?php

abstract class F5_Content_Abstract
{
    const NO_SETTER = 'no setter method';
    public $admin_id;
    public $content_id;
    public $content_parent_id;
    public $content_name;
    protected $content_type;        
    public $content_url;
    public $content_template;
    public $content_meta_title;
    public $content_meta_keywords;
    public $content_meta_description;
    public $content_order;
    protected $_contentObject;
    
    public function __construct($content_id = null, $content_url = null)
    {
        $this->_contentObject = new F5_Content_Content();

        if(!is_null($content_id)) {
            $this->loadContentObject(intval($content_id));
            
        } elseif(!is_null($content_url)) {
            $this->loadContentByUrl($content_url);
        }
    }
    
    protected function _getInnerRow($content_id = null)
    {
        if(is_null($content_id)) {
            $content_id = $this->content_id;
        }
        
        return $this->_contentObject->find($content_id)->current();
    }

    protected function _getProperties()
    {
        $propertyArray = array();        
        $class = new Zend_Reflection_Class($this);
        $properties = $class->getProperties();
        
        foreach ($properties as $property) {
            if ($property->isPublic()) {
                $propertyArray[] = $property->getName();
            }
        }
        return $propertyArray;
    }

    protected function _callSetterMethod($property, $data)
    {
        $method = Zend_Filter::filterStatic($property, 'Word_UnderscoreToCamelCase');
        $methodName = '_set' . $method;
        
        if (method_exists($this, $methodName)) {
            return $this->$methodName($data);
        } else {
        	return self::NO_SETTER;
        }
    }

    protected function loadContentByUrl($content_url)
    {
        if($content_id = $this->_contentObject->getContentId($this->content_type, $content_url)) {            
            $this->loadContentObject(intval($content_id));    	
        } else {
        	return FALSE;
        }
    }
    
    public function loadContentObject($content_id)
    {
        $this->content_id = $content_id;
        
        if($row = $this->_getInnerRow()) {
            if($row->content_type != $this->content_type) {
                return FALSE;
            }
            
            $this->admin_id                     = $row->admin_id;
            $this->content_id                   = $row->content_id;
            $this->content_parent_id            = $row->content_parent_id;
            $this->content_name                 = $row->content_name;
            $this->content_type                 = $row->content_type;    
            $this->content_url                  = $row->content_url;                 
            $this->content_template             = $row->content_template;
            $this->content_meta_title           = $row->content_meta_title;
            $this->content_meta_keywords        = $row->content_meta_keywords;
            $this->content_meta_description     = $row->content_meta_description;
            $this->content_order                = $row->content_order;
            $this->content_updated              = $row->content_updated;
            $this->content_created              = $row->content_created;
  
            $contentNode = new F5_Content_Node();
            $nodes = $row->findDependentRowset($contentNode);
            
            if($nodes) {
                $properties = $this->_getProperties();
                
                foreach ($nodes as $node) {
                    $key = $node['node_name'];
                    
                    if(in_array($key, $properties)) {

                        $value = $this->_callSetterMethod($key, $nodes);
                        
                        if($value === self::NO_SETTER) {
                            $value = $node['node_content'];
                        }
                        
                        $this->$key = $value;
                    }
                }
            }
        } else {
        	return FALSE;
        }
    }

    public function toArray()
    {
        $properties = $this->_getProperties();
        
        foreach ($properties as $property) {
            $array[$property] = $this->$property;
        }
        
        return $array;
    }

    public function save()
    {
        if(isset($this->content_id)) {
            return $this->_update();
        } else {
            return $this->_insert();
        }
    }

    protected function _insert()
    {
        $content = array(
            'admin_id'                  => $this->admin_id,
            'content_parent_id'         => $this->content_parent_id,
            'content_name'              => $this->content_name,
            'content_type'              => $this->content_type,        
            'content_url'               => $this->content_url,
            'content_template'          => $this->content_template,
            'content_meta_title'        => $this->content_meta_title,
            'content_meta_keywords'     => $this->content_meta_keywords,
            'content_meta_description'  => $this->content_meta_description,
            'content_order'             => $this->content_order
        );

        if($content_id = $this->_contentObject->createContent($content)) {
            $this->content_id = $content_id;
            $this->_update();
            return TRUE;
        } else {
        	return FALSE;
        }
    }

    protected function _update()
    {
        $content = $this->toArray();
        return $this->_contentObject->updateContent($this->content_id, $content);
    }

    public function delete()
    {
        if(isset($this->content_id)) {
            return $this->_contentObject->deleteContent($this->content_id);
        }
    }

    public function getContent($order = 'content_created DESC')
    {
        return $this->_contentObject->getContent($this->content_type, $order);
    }    
    
}