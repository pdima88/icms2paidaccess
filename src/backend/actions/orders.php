<?php
namespace pdima88\icms2paidaccess\backend\actions;

use Nette\Utils\Html;
use pdima88\icms2ext\crudAction;

/**
 * @property modelPaidaccess $model
 */

class orders extends crudAction {

    function getGrid() {

        $select = $this->model->orders->selectAs('o')
            ->joinLeftBy(tablePaidaccess_Orders::FK_USER, 'u')
            ->columns([
                'o.*',
                'user_fio' => 'CONCAT_WS(" ", u.lname, u.fname, u.mname)'
            ]);

        $grid = [
            'id' => 'orders',
            'select' => $select,
            'sort' => [
                'id' => 'desc',
            ],

            'multisort' => true,
            'paging' => 15,

            'url' => $this->cms_core->uri_absolute,
            'ajax' => $this->cms_core->uri_absolute,
            'actions' => Html::el('div', [
                'class' => 'datagrid'
            ])->setHtml(Html::el('div', [
                'class' => 'actions'
            ])->setHtml(Html::el('a', [
                    'title' => 'Изменить',
                    'class' => 'edit',
                    'href'  => href_to('admin', 'controllers', ['edit', $this->name, 'tariffs_edit', '{id}']) . '?back={returnUrl}'
                ]).Html::el('a', [
                    'title' => 'Удалить',
                    'class' => 'delete',
                    'href' => '',
                    'onclick' => 'return $.S4Y.grid.confirmDelete(this)',
                ]))),
            'delete' => href_to('admin', 'controllers', ['edit', $this->name, 'tariffs_delete', '{id}']). '?back={returnUrl}',
            'columns' => [
                'id' => [
                    'title' => 'ID',
                    'width' => 70,
                    'sort' => true,
                    'filter' => 'equal'
                ],
                'tariff_plan' => [

                ],
                'tariff_period' => [

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
