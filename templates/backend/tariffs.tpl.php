<?php
/** @var cmsTemplate $this */
$action = 'tariffs';
$this->renderAsset('icms2ext/backend/treeandgrid', [
    'tree' => $groups,
    'grid' => $grid,
    'id' => $plan_id,
    'page_title' => $page_title,
    'page_url' => $page_url,
    'treeitem_detail_url' => $this->href_to($action, 'plan_info'),
    'toolbar' => [
        'add' => [
            'title' => 'Новый тариф',
            'href'  => $this->href_to($action, ['add', '{id}']).'?back={returnUrl}',
        ],
        'add_folder' => [
            'title' => 'Новый тарифный план',
            'href'  => $this->href_to($action, 'plan_add').'?back={returnUrl}',
        ],
        'edit' => [
            'title' => 'Редактировать тарифный план',
            'href'  => $this->href_to($action, ['plan_edit', '{id}']).'?back={returnUrl}',
            'hide' => true,
        ],
        'delete' => [
            'title' => 'Удалить тарифный план',
            'href'  => $this->href_to($action, ['plan_delete', '{id}']).'?csrf_token='.cmsForm::getCSRFToken(),
            'onclick' => "return confirm('Все тарифы внутри тарифного плана также будут удалены!')",
            'hide' => true,
        ],
        'excel' => [
            'title' => 'Экспорт',
            'export' => 'csv',
            'target' => '_blank'
        ]
    ],
]);

