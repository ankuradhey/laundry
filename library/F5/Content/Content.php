<?php

class F5_Content_Content extends F5_Db_Table_Abstract
{
    protected $_name = 'default_content';

    protected $_dependentTables = array('F5_Content_Node');
    
    protected $_referenceMap    = array(
        'Content' => array(
            'columns'           => array('content_parent_id'),
            'refTableClass'     => 'F5_Content_Content',
            'refColumns'        => array('content_id'),
            'onDelete'          => self::CASCADE,
            'onUpdate'          => self::RESTRICT
        )
    );

    public function createContent($content)
    {
        if(empty($content) || !is_array($content)) {
        	return FALSE;
        }

        try {
            $row = $this->createRow();
            
            foreach ($content as $field => $value) {
                $row->$field = $value;
			}

            $row->content_created = time(); 
            $row->save();
            
            return $this->_db->lastInsertId();      	
            
        } catch (Exception $e) {
            return FALSE;
        }
    }

    public function updateContent($content_id, $content)
    {
        if(!$row = $this->find($content_id)->current()) {
        	return FALSE;
        }
 
        try {
            foreach ($content as $field => $value) {    
                if(isset($row->$field)){
                	$row->$field = $value;
                    unset($content[$field]);
                }
            }
            
            $row->content_updated = time();
            $row->save();

            if(count($content) > 0) {
                 $content_node = new F5_Content_Node();
                 
                 foreach ($content as $node_name => $node_content) {
                     $content_node->setNode($content_id, $node_name, $node_content);
                 }
            }
          
            return TRUE;
                    	
        } catch (Exception $e) {
            return FALSE;
        }        
    }

    public function deleteContent($content_id)
    {
        try {
			if($row = $this->find($content_id)->current()) {
                $row->delete();
            }
            
            return TRUE;
            
		} catch (Exception $e) {
		    return FALSE;
		}
    }
    
    public function getContent($content_type = NULL, $order)
    {
        try {
            if($content_type) {
                
                $pages = $this->fetchAll(
                            $this->select()
                                 ->setIntegrityCheck(false)
                                 ->order($order)
                                 ->where('content_type = ?', $content_type)
                );        
                    	
            } else {

                $pages = $this->fetchAll(
                            $this->select()
                                 ->setIntegrityCheck(false)
                                 ->order($order)
                );             	
            }
            
            return $pages;
            
        } catch (Exception $e) {
            return FALSE;
        }
    }

    public function getContentId($content_type = NULL, $content_url)
    {
        try {
            if($content_type) {

                $page = $this->fetchRow(
                           $this->select()
                                ->setIntegrityCheck(false)
                                ->where('content_url = ?', $content_url)                                
                                ->where('content_type = ?', $content_type)
                );
                                    
            } else {

                $page = $this->fetchRow(
                           $this->select()
                                ->setIntegrityCheck(false)
                                ->where('content_url = ?', $content_url)
                );            
            }
            
            return $page->content_id;
            
        } catch (Exception $e) {
            return FALSE;
        }
    }
}