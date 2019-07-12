<?php
namespace pdima88\icms2paidaccess\forms;

use pdima88\icms2ext\Format;
use cmsForm;
use fieldString;
use fieldText;
use fieldList;
use pdima88\icms2paidaccess\model;

class form_bonuscode_type extends cmsForm {

    public function init(){
        $model = model::getInstance();
        $tariffsList = ['all' => 'Все'];
        $tariffs = $model->getTariffs();

        $plans = $model->getTariffPlans();
        foreach ($plans as $plan) {
            $tariffsList['p'.$plan['id']] = $plan['title'].':все';
            foreach ($tariffs as $tariff) {
                if ($tariff['plan_id'] == $plan['id']) {
                    $tariffsList[$tariff['id']] = $plan['title'] . ':' . Format::formatDuration($tariff['period']);
                }
            }
        }

        $form = [
            [
                'type' => 'fieldset',
                'title' => 'Тип бонус-кодов',
                'childs' => [

                    new fieldString('title', [
                        'title' => 'Название',
                        'rules' => [
                            ['required'],
                        ]
                    ]),

                    new fieldText('hint', [
                        'title' => 'Описание',
                        'rules' => [
                            ['required'],
                        ]
                    ]),

                ]

            ],
            [
                'type' => 'fieldset',
                'title' => 'Параметры',
                'childs' => [
                    new fieldList('bonus_type', [
                        'title' => 'Тип бонуса',
                        'items' => [
                            'discount_percent' => 'Скидка, в %',
                            'discount_value' => 'Фиксированная скидка, в сумах'
                        ]
                    ]),

                    new fieldList('bonus_tariffs', [
                        'title' => 'Тарифы, на которые действует скидка',
                        'is_tree' => false,
                        'is_chosen_multiple' => true,
                        'items' => $tariffsList
                    ])
                ]
            ]
        ];

        return $form;
    }

    public function parse($request, $is_submitted=false, $item=false){
        $result = parent::parse($request, $is_submitted, $item);
        $data = [];
        $itemData = isset($item['data']) ? cmsModel::yamlToArray($item['data']) : [];
        $data['bonus_type'] = $result['bonus_type'] ?? $itemData['bonus_type'] ?? 'discount_percent';
        $data['bonus_tariffs'] = $result['bonus_tariffs'] ?? $itemData['bonus_tariffs'] ?? [];
        $result['data'] = cmsModel::arrayToYaml($data);
        $result['bonus_type'] = $data['bonus_type'];
        $result['bonus_tariffs'] = $data['bonus_tariffs'];
        return $result;
    }
}
