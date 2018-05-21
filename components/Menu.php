<?php

namespace SamPoyigi\Local\Components;

use Admin\Models\Categories_model;
use Admin\Models\Menus_model;

class Menu extends \System\Classes\BaseComponent
{
    protected $location;

    protected $list = [];

    public function defineProperties()
    {
        return [
            'isGrouped'       => [
                'label' => 'Group Menu Items',
                'type'  => 'switch',
            ],
            'menusPerPage'    => [
                'label'   => 'Menus Per Page',
                'type'    => 'number',
                'default' => 20,
            ],
            'showMenuImages'  => [
                'label' => 'Show Menu Item Images',
                'type'  => 'switch',
            ],
            'menuImageWidth'  => [
                'label'   => 'lang:sampoyigi.local::default.label_menu_image_height',
                'type'    => 'number',
                'span'    => 'left',
                'default' => 95,
            ],
            'menuImageHeight' => [
                'label'   => 'lang:sampoyigi.local::default.label_menu_image_width',
                'type'    => 'number',
                'span'    => 'right',
                'default' => 80,
            ],
        ];
    }

    public function onRun()
    {
        $isGrouped = $this->property('isGrouped');
        $this->page['menuIsGrouped'] = $isGrouped;
        $this->page['showMenuImages'] = $this->property('showMenuImages');

        $this->page['menuList'] = $isGrouped ?
            $this->loadGroupedList() : $this->loadList();
    }

    protected function loadList()
    {
        $list = Menus_model::with(['mealtime', 'menu_options'])->listFrontEnd([
            'page'      => $this->param('page'),
            'pageLimit' => $this->property('menusPerPage'),
            'sort'      => $this->property('sort', 'menu_priority asc'),
            'category'  => $this->param('category'),
        ]);

        return $list;
    }

    protected function loadGroupedList()
    {
        $query = Categories_model::with([
            'menus' => function ($menusQuery) {
                $menusQuery->listFrontEnd([
                    'page'      => $this->param('page'),
                    'pageLimit' => $this->property('menusPerPage'),
                    'sort'      => $this->property('sort', 'menu_priority asc'),
                    'group'     => null, //$this->property('group', 'categories.category_id'),
                    'category'  => null, //$this->param('category'),
                ]);
            },
        ]);

//        $query->whereHasMenus();

        if ($this->param('category'))
            $query->whereSlug($this->param('category'));

        $list = $query->get();

        return $list;
    }
}