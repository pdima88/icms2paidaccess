<?php
namespace pdima88\icms2paidaccess\actions;

use cmsAction;
use pdima88\icms2paidaccess\model;
use cmsUser;
use tableUsers;
use cmsTemplate;
use pdima88\icms2paidaccess\frontend;

/**
 * @property model $model
 * @property frontend $controller
 */
class demo extends cmsAction {

    public function run()
    {
        $this->controller->checkEmailConfirmed();

        $status = 0;
        $form = null;

        $demo = $this->model->demo->getByUserId(cmsUser::getId());

        if (!$demo) {

            if (cmsUser::get('regstatus') < 2) {
                $status = 1;
                $user = tableUsers::getById(cmsUser::getInstance()->id)->toArray();
                $form = $this->getForm('user_info', [$user]);
            }
        }

        cmsTemplate::getInstance()->render('demo', [
            'status' => $status,
            'form' => $form
        ]);
        
    }

}