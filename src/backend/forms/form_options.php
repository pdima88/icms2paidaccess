<?php
namespace pdima88\icms2paidaccess\backend\forms;

use cmsForm;
use fieldListGroups;
use fieldNumber;

class form_options extends cmsForm {
    //public $is_tabbed = true;
    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => 'Демо-доступ',
                'childs' => array(

                    new fieldListGroups('demo_groups', array(
                        'title' => 'Группы, присваиваемые пользователю при активации демо-доступа',
                    )),

                    new fieldNumber('demo_period', array(
                        'title' => 'Срок демо-доступа (дней)',
                        'hint' => '0 - бессрочно'
                    ))

                )
            ),


        );

    }

}
