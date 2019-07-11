<?php
namespace pdima88\icms2paidaccess\backend\actions;

use Nette\Utils\Html;
use pdima88\icms2ext\crudAction;
use pdima88\icms2paidaccess\tables\table_orders;
use pdima88\icms2ext\GridHelper;
use pdima88\icms2paidaccess\model;

/**
 * @property model $model
 */

class orders extends crudAction {

    protected $indexTpl = 'grid';
    protected $pageTitle = 'Заказы';

    function getGrid() {

        $select = $this->model->orders->selectAs('o')
            ->joinLeftBy(table_orders::FK_USER, 'u')
            ->columns([
                'o.*',
                'user_name' => 'u.nickname',
                'user_email' => 'u.email',
                'user_phone' => 'u.phone'
            ]);

        $tariffPlans = $this->model->getTariffPlansList();

        $grid = [
            'id' => 'orders',
            'select' => $select,
            'sort' => [
                'id' => 'desc',
            ],

            'multisort' => true,
            'paging' => 10,
            'rownum' => false,
            'url' => $this->cms_core->uri_absolute,
            'ajax' => $this->cms_core->uri_absolute,
            'actions' => GridHelper::getActions([
                'edit' => [
                    'title' => 'Изменить',                    
                    'href'  => href_to('admin', 'controllers', ['edit', $this->name, 'tariffs_edit', '{id}']) . '?back={returnUrl}'
                ],
                'delete' => [
                    'title' => 'Удалить',                    
                    'href' => '',
                    'onclick' => 'return $.S4Y.grid.confirmDelete(this)',
                ]
            ]),
            'delete' => href_to('admin', 'controllers', ['edit', $this->name, 'tariffs_delete', '{id}']). '?back={returnUrl}',
            'columns' => [
                'id' => [
                    'title' => 'ID заказа',
                    'width' => 70,
                    'sort' => true,
                    'filter' => 'equal'
                ],                
                'user_id' => [
                    'title' => 'ID польз.',
                    'width' => 70,
                    'sort' => true,
                    'filter' => 'equal'
                ],
                'user_name' => [
                    'title' => 'Ф.И.О. пользователя',
                    'sort' => true,
                    'filter' => 'text'
                ],
                'user_email' => [
                    'title' => 'E-mail',
                    'sort' => true,
                    'filter' => 'text'
                ],
                'user_phone' => [
                    'title' => 'Номер телефона',
                    'sort' => true,
                    'filter' => 'text'
                ],
                'plan_id' => [
                    'title' => 'Тарифный план',
                    'format' => $tariffPlans,
                    'filter' => 'select',
                    'sort' => true,
                ],
                'period' => [
                    'title' => 'Срок подписки (дней)',
                    'align' => 'center',
                    'width' => 70,
                    'sort' => true,
                    'filter' => 'equal'
                ],
                'discount' => [
                    'title' => 'Скидка',
                    'sort' => true,
                    'filter' => 'text'
                ],
                'total_amount' => [
                    'title' => 'Цена с учетом скидки',
                    'format' => 'format_currency',
                    'align' => 'right',
                    'width' => 100,
                    'sort' => true,
                    'filter' => 'equal'
                ],
            ]
        ];
        return $grid;
    }

}
