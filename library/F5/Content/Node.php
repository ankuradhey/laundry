<?php

class F5_Content_Node extends F5_Db_Table_Abstract
{
    protected $_name = 'default_nodes';

    protected $_referenceMap = array(
        'Content' => array(
            'columns'        => array('content_id'),
            'refTableClass'  => 'F5_Content_Content',
            'refColumns'     => array('content_id'),
            'onDelete'       => self::CASCADE,
            'onUpdate'       => self::RESTRICT
        )
    );

    public function setNode($content_id, $node_name, $node_content)
    {
        $row = $this->fetchRow(
                   $this->select()
                        ->setIntegrityCheck(false)
                        ->where("content_id = ?", $content_id)
                        ->where("node_name = ?", $node_name)
        );
            
        if(!$row) {
            $row = $this->createRow();
            $row->content_id = $content_id;
            $row->node_name = $node_name;
        }

        $row->node_content = $node_content;
        $row->save();
    }
}