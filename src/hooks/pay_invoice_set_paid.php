<?php
namespace pdima88\icms2paidaccess\hooks;

use cmsAction;
use cmsUser;
use pdima88\icms2paidaccess\frontend as paidaccess;
use pdima88\icms2paidaccess\tables\row_order;
use pdima88\icms2pay\tables\row_invoice;

/**
 * @mixin paidaccess
 */
class pay_invoice_set_paid extends cmsAction
{
    /**
     * @param $data
     * @return bool
     */
    public function run($data)
    {
        $invoice = $data['invoice'];
        if ($invoice) {
            /** @var row_order $order */
            $order = $this->model->orders->fetchRow(['invoice_id = ?' => $invoice['id']]);
            if ($order && $order->id == $invoice['order_id'] &&
                !$order->date_paid && !$order->date_cancelled && !$order->date_activated &&
                !$order->is_active) {

                $order->date_paid = $data['date_paid'];
                $order->pay_type = $data['pay_type'];
                $order->save();

                // личное сообщение
                $this->controller_messages->addRecipient($order->user_id)->sendNoticePM(array(
                    'content' => 'Заказ №'.$order->id.' оплачен. Активируйте заказ на вкладке Платный доступ на странице Мой профиль',
                    'actions' => array(
                        'activate' => array(
                            'title' => 'Активировать',
                            'href'  => href_to($this->name, 'activate', [$order->id])
                        )
                    )
                ));
                return true;
            }
        }
        
        return false;
    }
}
