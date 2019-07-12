<?php
namespace pdima88\icms2paidaccess\actions;

use cmsAction;
use pdima88\icms2paidaccess\model;
use cmsUser;
use tableUsers;
use cmsTemplate;

/**
 * @property model $model
 */
class demo extends cmsAction {

    public function run()
    {
        $status = 0;
        $form = null;
        if (cmsUser::get('regstatus') < 2) {
            $status = 1;
            $user = tableUsers::getById(cmsUser::getInstance()->id)->toArray();
            $form = $this->getForm('user_info', [$user]);
        }

        cmsTemplate::getInstance()->render('demo', [
            'status' => $status,
            'form' => $form
        ]);
    }

}