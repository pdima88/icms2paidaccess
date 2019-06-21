<?php
namespace pdima88\icms2paidaccess\backend\actions;

use Nette\Utils\Html;
use pdima88\icms2ext\crudAction;
use pdima88\icms2ext\Format;
use pdima88\icms2ext\GridHelper;
use cmsTemplate;
use cmsCore;
use cmsUser;
use Exception;
use pdima88\icms2paidaccess\model as modelPaidaccess;

/**
 * @property modelPaidaccess $model
 */
class tariffs extends crudAction
{

    const FORM_TARIFF = 'tariff';
    const FORM_TARIFF_PLAN = 'tariff_plan';

    public function __construct($controller, array $params)
    {
        parent::__construct($controller, $params);

        $this->pageTitle = 'Тарифные планы';
        $this->titles['add'] = 'Добавление тарифа';
        $this->titles['edit'] = 'Редактирование тарифа';
        $this->titles['plan_add'] = 'Добавление тарифного плана';
        $this->titles['plan_edit'] = 'Редактирование тарифного плана';
        $this->messages['add'] = 'Тариф добавлен';
        $this->messages['error_edit_no_item'] = 'Тариф не найден';
    }

    public function actionIndex() {
        $res = parent::actionIndex();

        $tariffPlans = $this->model->getTariffPlans();
        $tariffPlans = is_array($tariffPlans) ? $tariffPlans : [];
        $groups = array_pad($tariffPlans, (sizeof($tariffPlans)+1)*-1, array(
                'id' => 0,
                'title' => LANG_ALL)
        );
        $res['data']['plan_id'] = $this->getParam();
        $res['data']['groups'] = $groups;
        return $res;
    }

    public function actionPlanInfo() {
        $id = $this->getParam(0);
        if (!$id) { exit; }

        $errors = false;

        $item = $this->model->getTariffPlan($id);

        if (!$item) {
            echo 'Тарифный план не найден!';
            exit;
        }
        $groupsArr = [];
        $groups = $this->model_users->getGroups();
        if ($groups) {
            foreach ($groups as $groups) {
                $groupsArr[$groups['id']] = $groups['title'];
            }
        }

        $tpl = cmsTemplate::getInstance();
        return $tpl->render('backend/tariff_plan_info', array(
            'item' => $item,
            'groups' => $groupsArr
        ));
    }

    public function getGrid()
    {
        $planId = $this->getParam();
        $select = $this->model->tariffs->selectAll();
        if (isset($planId) && $planId) $select->where('plan_id = ?', $planId);

        $grid = [
            'id' => 'tariffs',
            'select' => $select,
            'rownum' => true,
            'sort' => [
                'period' => 'asc',
            ],

            'multisort' => false,
            'paging' => 10,

            'url' => $this->cms_core->uri_absolute,
            'ajax' => $this->cms_core->uri_absolute,
            'actions' => GridHelper::getActions([
                'edit' => [
                    'title' => 'Изменить',
                    'class' => 'edit',
                    'href'  => href_to('admin', 'controllers', ['edit', $this->name, 'tariffs', 'edit', '{id}']) . '?back={returnUrl}'
                ],
                'delete' => [
                    'title' => 'Удалить',
                    'href' => '',
                    'confirmDelete' => true
                ]
            ]),
            'delete' => href_to('admin', 'controllers', ['edit', $this->name, 'tariffs', 'delete', '{id}']). '?back={returnUrl}',
            'columns' => []
        ];

        if (!isset($planId) || !$planId) {
            $grid['columns']['plan_id'] = [
                'title' => 'Тарифный план',
                'filter' => 'select',
                'sort' => 'true',
                'format' => $this->model->getTariffPlansList()
            ];
            $grid['sort'] = ['plan_id' => 'asc'] + $grid['sort'];
            $grid['multisort'] = true;
        }
        $grid['columns']['tariff_name'] = [
            'title' => 'Тариф',
            'width' => 150,
            'format' => __CLASS__.'::formatTariffName'
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
            'format' => 'format_currency',
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

    static function formatTariffName($value, $row) {
        if ($row['period'] ?? false) {
            return Format::formatDuration($row['period']);
        }
        return '';
    }

    public function setForm($formName) {
        $this->formName = $formName;
        if ($formName == self::FORM_TARIFF) {
            $this->tableName = modelPaidaccess::TABLE_TARIFFS;
        } elseif ($formName == self::FORM_TARIFF_PLAN) {
            $this->tableName = modelPaidaccess::TABLE_TARIFF_PLANS;
        } else {
            throw new Exception('Unknown form name: '.$formName);
        }
    }

    public function actionAdd()
    {
        $this->setForm(self::FORM_TARIFF);
        $result = parent::actionAdd();
        if (!isset($result['data']['item']['plan_id'])) {
            $plan_id = $this->getParam();
            if (isset($plan_id)) $result['data']['item']['plan_id'] = $plan_id;
        }
        return $result;
    }

    public function actionEdit($id = null, $item = null)
    {
        $this->setForm(self::FORM_TARIFF);
        return parent::actionEdit();
    }

    public function actionDelete() {
        $this->setForm(self::FORM_TARIFF);
        return parent::actionDelete();
    }

    public function actionPlanAdd() {
        $this->setForm(self::FORM_TARIFF_PLAN);
        return parent::actionAdd();
    }

    public function actionPlanEdit() {
        $this->setForm(self::FORM_TARIFF_PLAN);
        $id = $this->getParam();
        if (!$id) cmsCore::error404();
        $item = $this->model->getTariffPlan($id);
        if (!$item) {
            cmsUser::addSessionMessage('Тарифный план не найден', 'error');
            $this->redirectBack();
        }
        return parent::actionEdit($id, $item);
    }

    public function actionPlanDelete() {
        $this->setForm(self::FORM_TARIFF_PLAN);
        $id = $this->getParam();
        if (!$id) cmsCore::error404();
        $item = $this->model->getTariffPlan($id);
        if (!$item) {
            cmsUser::addSessionMessage('Тарифный план не найден', 'error');
            $this->redirectBack();
        }
        return parent::actionDelete();
    }
}