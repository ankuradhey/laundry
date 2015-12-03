<?php

namespace Application\V1\Rest\Category;

class CategoryEntity {

    public $category_id;
    public $category_name;
    public $category_order;
    public $is_live;
    public $cat_image;

    public function getArrayCopy() {
        return array(
            'category_id' => $this->category_id,
            'category_name' => $this->category_name,
            'category_order' => $this->category_order,
            'is_live' => $this->is_live,
            'cat_image' => $this->cat_image,
        );
    }

    public function exchangeArray(array $array) {
        $this->category_id = $array['category_id'];
        $this->category_name = $array['category_name'];
        $this->category_order = $array['category_order'];
        $this->is_live = $array['is_live'];
        $this->cat_image = $array['cat_image'];
    }

}
