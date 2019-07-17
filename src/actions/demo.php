<?php
namespace pdima88\icms2paidaccess\actions;

use cmsAction;
use pdima88\icms2paidaccess\model;
use cmsUser;
use tableUsers;
use cmsTemplate;
use pdima88\icms2paidaccess\frontend;
use cmsForm;

/**
 * @property model $model
 * @property frontend $controller
 * @mixin frontend
 */
class demo extends cmsAction {

    public function run()
    {
        $this->checkEmailConfirmed('Перед активацией демо доступа необходимо подтвердить e-mail');

        $options = $this->getOptions();

        $status = 0;
        $form = null;
        $errors = null;
        $demoPeriod = $options['demo_period'] ?? 0;
        $success = false;

        $demo = $this->model->demo->getByUserId(cmsUser::getId());
        $data = [];

        if (!$demo) {

            if (cmsUser::get('regstatus') < 2) {
                $status = 1;
                $user = tableUsers::getById(cmsUser::getId())->toArray();
                $form = $this->getForm('userinfo', [$user]);
                $data = $user;

                if ($this->request->has('submit')) {
                    $data = $form->parse($this->request, true, $user);
                    $errors = $form->validate($this, $data);

                    if (!$errors) {
                        $data['regstatus'] = 2;
                        $this->model_users->updateUser(cmsUser::getId(), $data);
                    }

                }
            } else {
                $status = 2;
                $form = new cmsForm();            
            }

            if ($this->request->has('submit') && !$errors) {
                $this->model->demo->insertRow([
                    'user_id' => cmsUser::getId(),
                    'when_activated' => now(),
                    'when_expiried' => $demoPeriod ? strtotime(now().' +'.$demoPeriod.' days') : null
                ]);
                $this->model->refreshByUserId(cmsUser::getId());
                $success = true;
                $form = false;
            }
        }

        cmsTemplate::getInstance()->render('demo', [
            'status' => $status,
            'form' => $form,
            'data' => $data,
            'demoPeriod' => $demoPeriod,
            'errors' => $errors,
            'success' => $success
        ]);
        
    }

}