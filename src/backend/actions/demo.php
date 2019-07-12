<?php
namespace pdima88\icms2paidaccess\backend\actions;

use Nette\Utils\Html;
use pdima88\icms2ext\crudAction;
use pdima88\icms2paidaccess\model;
use pdima88\icms2paidaccess\tables\table_demo;
use pdima88\icms2ext\GridHelper;

/**
 * @property model $model
 */

class demo extends crudAction {

    protected $indexTpl = 'grid';
    protected $pageTitle = 'Демо-доступ';

    function getGrid() {

        $select = $this->model->demo->selectAs('d')
            ->joinLeftBy(table_demo::FK_USER, 'u')
            ->columns([
                'd.*',
                'fullname' => 'CONCAT_WS(" ", u.lname, u.fname, u.mname)',
                'u.email',
                'u.phone'
            ]);

        $grid = [
            'id' => 'demo',
            'select' => $select,
            'sort' => [
                'when_activated' => 'desc',
            ],
            'actions' => GridHelper::getActions([
                'delete' => [
                    'title' => 'Удалить',
                ]
            ]),
            'multisort' => true,
            'paging' => 15,

            'url' => $this->cms_core->uri_absolute,
            'ajax' => $this->cms_core->uri_absolute,           
            'columns' => [                
                'user_id' => [
                    'title' => 'ID пользователя',
                    'width' => '100',
                    'filter' => 'equal',
                    'sort' => true,
                ],
                'name' => [
                    'title' => 'Имя пользователя',
                    'filter' => 'text',
                    'sort' => true,
                ],
                'email' => [
                    'title' => 'E-mail',
                    'filter' => 'text',
                    'sort' => true,
                ],
                'phone' => [
                    'title' => 'Номер телефона',
                    'filter' => 'text',
                    'sort' => true,
                ],
                'when_activated' => [
                    'title' => 'Дата/время активации',
                    'filter' => 'dateRange',
                    'sort' => true,
                    'filter-opens' => 'left',
                ],
                'when_expiried' => [
                    'title' => 'Дата/время действия',
                    'filter' => 'dateRange',
                    'sort' => true,
                    'filter-opens' => 'left',
                ],
                
            ]
        ];

        return $grid;
    }

}
