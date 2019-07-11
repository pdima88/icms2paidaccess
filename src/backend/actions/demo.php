<?php
namespace pdima88\icms2paidaccess\backend\actions;

use Nette\Utils\Html;
use pdima88\icms2ext\crudAction;
use pdima88\icms2paidaccess\tables\table_orders;
use pdima88\icms2paidaccess\model;
use pdima88\icms2paidaccess\tables\table_demo;

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

                ],
                'when_activated' => [
                    'title' => 'Дата/время активации',
                    'filter' => 'dateRange',
                    'sort' => true,
                ],
                'user_id' => [
                    'title' => 'ID пользователя',
                    'width' => 70,
                    'sort' => true,
                    'filter' => 'equal'
                ],
                'user_fio' => [
                    'title' => 'Ф.И.О. пользователя',
                    'sort' => true,
                    'filter' => 'text'
                ]
            ]
        ];

        $grid['columns']['period'] = [
            'title' => 'Срок подписки (дней)',
            'align' => 'center',
            'width' => 150,
            'sort' => true,
            'filter' => 'equal'
        ];
        $grid['columns']['price'] = [
            'title' => 'Цена',
            'format' => '%.f',
            'width' => 100,
            'align' => 'right',
            'sort' => true,
            'filter' => 'equal'
        ];
        $grid['columns']['bonus'] = [
            'title' => 'Кол-во бонусных вопросов',
            'align' => 'center',
            'sort' => true,
            'filter' => 'equal'
        ];
        $grid['columns']['is_active'] = [
            'title' => 'Тариф активен',
            'width' => 120,
            'align' => 'center',
            'sort' => true,
            'format' => 'checkbox',
            'filter' => 'select',
        ];
        $grid['columns']['orders_count'] = [
            'title' => 'Кол-во оплаченных заказов',
            'sort' => true,
        ];

        return $grid;
    }

}
