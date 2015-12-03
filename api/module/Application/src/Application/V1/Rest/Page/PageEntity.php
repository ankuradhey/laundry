<?php

namespace Application\V1\Rest\Page;

class PageEntity {

    public $page_id;
    public $page_key;
    public $page_name;
    public $page_content;

    public function getArrayCopy() {
        return array(
            'page_id' => $this->page_id,
            'page_key' => $this->page_key,
            'page_name' => $this->page_name,
            'page_content' => $this->page_content,
        );
    }

    public function exchangeArray(array $array) {
        $this->page_id = $array['page_id']; 
        $this->page_key = $array['page_key']; 
        $this->page_name = $array['page_name']; 
        $this->page_content = $array['page_content']; 
    }

}
