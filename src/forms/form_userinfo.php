<?php
namespace pdima88\icms2paidaccess\forms;

use fieldDate;
use cmsForm;
use fieldString;
use fieldOrgTax;
use pdima88\icms2bonuscode\frontend;

/** @property frontend $controller */
class form_userinfo extends cmsForm {

    public function init($user){
        $form = array(

            'basic' => [
                'type' => 'fieldset',
                'title' => 'Укажите ваши данные',
                'childs' => [
                    new fieldString('lname', [
                        'title' => 'Фамилия',
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('fname', [
                        'title' => 'Имя',
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('mname', [
                        'title' => 'Отчество',
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('phone', [
                        'title' => 'Номер телефона',
                        'rules' => [
                            ['required']
                        ],
                    ]),
                    new fieldString('jobpost', [
                        'title' => 'Должность',
                        'rules' => [
                            ['required']
                        ],
                    ]),
                    new fieldString('joborg', [
                        'title' => 'Название организации',
                        'rules' => [
                            ['required']
                        ],
                    ]),
                    new fieldString('orgtype', [
                        'title' => 'Вид деятельности организации',
                        'rules' => [
                            ['required']
                        ],
                    ]),
                    new fieldDate('orgdate', [
                        'title' => 'Дата регистрации организации',
                        'options' => [
                            'current' => false
                        ]
                    ]),
                    new fieldOrgTax('orgtax', [
                        'title' => 'Система налогообложения в организации',
                    ]),
                ]
            ],


        );

        return $form;
    }

    
}
