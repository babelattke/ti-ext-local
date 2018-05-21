<?php

namespace SamPoyigi\Local\Components;

use Admin\Models\Reviews_model;
use Location;

class Review extends \System\Classes\BaseComponent
{
    public $defaultPartial = 'list';

    public $isHidden = TRUE;

    public function onRun()
    {
        $this->id = uniqid($this->alias);
        $this->page['reviewList'] = $this->loadReviewList();
    }

    protected function loadReviewList()
    {
        if (!$location = Location::current())
            return null;

        $list = Reviews_model::listFrontEnd([
            'page'      => $this->param('page'),
            'pageLimit' => $this->property('pageLimit'),
            'sort'      => $this->property('sort', 'date_added asc'),
            'location'  => $location->getKey(),
        ]);

        return $list;
    }
}