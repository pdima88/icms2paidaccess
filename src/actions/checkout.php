<?php
namespace pdima88\icms2paidaccess\actions;

use cmsCore;
use cmsForm;
use cmsAction;
use cmsUser;
use cmsTemplate;
use pdima88\icms2paidaccess\frontend;
use pdima88\icms2paidaccess\model;
use pdima88\icms2paidaccess\tables\table_orders;
use pdima88\icms2pay\fields\field_paytype;
use pdima88\icms2pay\model as modelPay;
use pdima88\icms2pay\frontend as pay;
use tableUsers;

/**
 * @property model $model
 * @property modelPay $model_pay
 * @property pay $controller_pay
 * @property frontend $controller
 * @mixin frontend
 */
class checkout extends cmsAction
{
    public function run($orderId = null) {
        $this->checkEmailConfirmed();

        $template = cmsTemplate::getInstance();

        $order = $this->model->orders->getById($orderId);
        if (!$order || $order->user_id != cmsUser::getId() || $order->date_cancelled) {
            cmsCore::error404();
        }
        
        $errors = [];

        $tariff = $order->tariff;
        if (!$tariff) $errors[] = 'Тариф не найден';
        elseif (!$tariff->is_active) $errors[] = 'Тариф отключен';
        
        $plan = $order->plan;
        if (!$plan) $errors[] = 'Тарифный план не найден';

        if ($order->date_paid) {
            $this->redirectToAction('activate', [$orderId]);
        }

        if (!$errors) {
            $user = tableUsers::getById(cmsUser::getId())->toArray();

            if ($user['regstatus'] < tableUsers::REG_STATUS_JOB) {
                $form = $this->getForm('userinfo', [$user]);
            } else {
                $form = new cmsForm();
            }
            if (!$order->date_paid && $order->pay_type == 'pay') {
                $form->addFieldset('Выберите способ оплаты', 'select_paytype');
                $form->addField('select_paytype', new field_paytype('pay_type', [

                ]));
            }

            if ($this->request->has('submit')) {
                if ($user['regstatus'] < tableUsers::REG_STATUS_JOB) {
                    $user = $form->parse($this->request, true, $user);
                    $errors = $form->validate($this, $user);


                    if (!$errors) {
                        $user['regstatus'] = 2;
                        $this->model_users->updateUser(cmsUser::getId(), $user);
                    }
                }

                if (!$errors) {
                    $payType = $this->request->get('submit');
                    $payTypes = $this->controller_pay->getPayTypes();
                    if ($order->total_amount == 0) {
                        $order->date_paid = now();
                        if ($order->bonuscode_id) {
                            $order->pay_type = table_orders::PAY_TYPE_BONUS;
                        } else {
                            $order->pay_type = table_orders::PAY_TYPE_FREE;
                        }
                        $order->save();
                        $this->redirectToAction('activate', [$order->id]);
                    } else {
                        if (!isset($payTypes[$payType])) {
                            $errors[] = 'Тип оплаты не найден';
                        } else {
                            $invoice = $order->makeInvoice();
                            $invoice->pay_type = $payType;
                            $invoice->save();
                            $order->save();
                            $this->redirectTo('pay', $payType, ['payment', $invoice->id]);
                        }
                    }
                }
            }
        }

        return $template->render('checkout', array(
            'tariff' => $tariff,
            'order' => $order,
            'plan' => $plan,
            'tariff' => $tariff,
            'user' => $user,
            'form' => $form,
            'errors' => $errors,
        ));
    }
}
