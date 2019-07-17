<?php
namespace pdima88\icms2paidaccess\actions;

use cmsCore;
use cmsForm;
use pdima88\icms2ext\Format;
use cmsAction;
use cmsUser;
use cmsTemplate;
use pdima88\icms2paidaccess\frontend;
use pdima88\icms2paidaccess\model;
use pdima88\icms2pay\fields\field_paytype;
use pdima88\icms2pay\model as modelPay;
use tableUsers;

/**
 * @property model $model 
 * @property frontend $controller
 * @mixin frontend
 */
class activate extends cmsAction
{
    public function run($orderId = null) {
        if (!cmsUser::isLogged()) cmsUser::goLogin();

        $template = cmsTemplate::getInstance();

        $errors = [];
        $user = null;
        $form = false;

        $order = $this->model->orders->getById($orderId);
        if (!$order ||
            $order->user_id != cmsUser::getId()) {
            $errors[] = 'Заказ не найден';
            $order = false;
        } elseif ($order->date_activated) {
            $errors[] = 'Заказ уже был активирован';
        } elseif (!$order->date_paid) {
            $errors[] = 'Заказ не оплачен';
        }

        if (empty($errors)) {
            $user = tableUsers::getById(cmsUser::getId())->toArray();

            if ($user['regstatus'] < tableUsers::REG_STATUS_JOB) {
                $form = $this->getForm('userinfo', [$user]);

                if ($this->request->has('submit')) {                    
                    $user = $form->parse($this->request, true, $user);
                    $errors = $form->validate($this, $user);

                    if (!$errors) {
                        $user['regstatus'] = 2;
                        $this->model_users->updateUser(cmsUser::getId(), $user);
                    }
                }
            } else {
                $form = new cmsForm();
            }
        }

        if ($this->request->has('submit')) {
            if ($user && $user['regstatus'] < tableUsers::REG_STATUS_JOB) {
                $user = $form->parse($this->request, true, $user);
                $errors = $form->validate($this, $user);

                if (!$errors) {
                    $user['regstatus'] = 2;
                    $this->model_users->updateUser(cmsUser::getId(), $user);
                }

            }
            
            if (!$errors) {
                $order->activate();
                $form = false;
            }
        }

        return $template->render('activate', array(
            'order' => $order,
            'tariff' => $order->tariff,
            'plan' => $order->plan,
            'user' => $user,
            'form' => $form,
            'errors' => $errors,
        ));
    }
}
