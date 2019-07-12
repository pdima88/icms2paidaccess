<?php
namespace pdima88\icms2paidaccess\hooks;

use cmsAction;
use fieldList;
use fieldListMultiple;

class bonuscode_type_form extends cmsAction {

    public function run($component, $form){
        if ($component == $this->controller->name) {
            $form[] = [
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

                    new fieldListMultiple('bonus_tariffs', [
                        'title' => 'Тарифы, на которые действует скидка',
                        'items' => [
                            '1' => 1
                        ]
                    ])
                ]
            ];
        }
        return $form;
    }

}
