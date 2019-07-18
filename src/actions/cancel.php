<?php
namespace pdima88\icms2paidaccess\actions;

use cmsAction;
use cmsForm;
use cmsUser;
use cmsTemplate;
use pdima88\icms2paidaccess\frontend;
use pdima88\icms2paidaccess\model;
use pdima88\icms2bonuscode\model as modelBonuscode;
use pdima88\icms2pay\tables\table_invoices;

/**
 * @property model $model
 * @property modelPay $model_pay
 * @property modelBonuscode $model_bonuscode
 * @property pay $controller_pay
 * @property frontend $controller
 * @mixin frontend
 */
class cancel extends cmsAction
{
    public function run($order_id){
        if (!cmsUser::isLogged()) cmsUser::goLogin();

        $template = cmsTemplate::getInstance();
        $success = false;
        $form = false;
        $errors = [];

        $order = $this->model->orders->getById($order_id);

        if (!$order || $order->user_id != cmsUser::getId()) {
            cmsCore::error404();
        }
        
        if ($order->date_cancelled) {
            $success = true;
        } else {
            if ($order->date_paid || $order->date_activated) {
                $errors[] = 'Нельзя отменить оплаченный или активированный заказ';
            }
            $form = new cmsForm();
            if (!$errors && $this->request->has('submit')) {
                $invoice = $order->invoice;
                if ($invoice) {
                    $invoice->date_cancel = now();
                    $invoice->cancelled_by_user_id = cmsUser::getId();
                    $invoice->status = table_invoices::STATUS_CANCELLED;
                    $invoice->save();
                }
                $order->date_cancelled = now();
                $order->save();
                $success = true;
            }
        }

        return $template->render('cancel', array(
            'form' => $form,
            'success' => $success,
            'order' => $order,
            'errors' => $errors,
        ));
    }

}
