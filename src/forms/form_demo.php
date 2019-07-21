<?php
namespace pdima88\icms2paidaccess\forms;

use pdima88\icms2ext\Format;
use cmsForm;
use fieldString;
use fieldText;
use fieldList;
use pdima88\icms2bonuscode\frontend;
use formAuthConfirm;

/** @property frontend $controller */
class form_demo extends cmsForm {

    public function init($user){
        $form = parent::init($user);


        return $form;
    }

    
}
