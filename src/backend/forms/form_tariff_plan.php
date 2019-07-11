<?php
namespace pdima88\icms2paidaccess\backend\forms;

use cmsCore;
use cmsForm;
use fieldCheckbox;
use fieldString;
use fieldHtml;
use fieldListMultiple;
use fieldNumber;

class form_tariff_plan extends cmsForm {

    public function init(){

        return [
            [
                'type' => 'fieldset',
                'title' => 'Тарифный план',
                'childs' => [
                    new fieldCheckbox('is_active', [
                        'title' => 'Активен',
                        'default' => false,
                    ]),

                    new fieldString('title', [
                        'title' => 'Название',
                        'rules' => [
                            ['required'],
                        ]
                    ]),

                    new fieldHtml('hint', [
                        'title' => 'Описание',
                        'rules' => [
                            ['required'],
                        ]
                    ]),

                    new fieldListMultiple('groups', [
                        'title' => 'Присвоить группу пользователей',
                        'generator' => function ($data) {

                            $groups = cmsCore::getModel('users')->getGroups();

                            if ($groups) {
                                foreach ($groups as $groups) {
                                    $items[$groups['id']] = $groups['title'];
                                }
                            }

                            return $items;

                        },

                    ]),

                    new fieldNumber('level', [
                        'title' => 'Уровень доступа',
                        'hint' => 'Число от 1 до N. (0 - зарезервировано для демо доступа). Тарифные планы с большим уровнем доступа включают все, что входит в тарифные планы с меньшим уровнем доступа'
                    ])

                ]

            ],
        ];
    }

}
