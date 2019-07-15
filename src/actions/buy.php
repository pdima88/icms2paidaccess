<?php
namespace pdima88\icms2paidaccess\actions;

use pdima88\icms2ext\Format;
use cmsAction;
use cmsUser;
use cmsTemplate;
use cmsRequest;
use pdima88\icms2paidaccess\frontend;

/**
 * @property modelPaidaccess $model
 * @property modelPay $model_pay
 * @property pay $controller_pay
 * @property frontend $controller
 * @mixin frontend
 */
class buy extends cmsAction
{
    public function run($plan_id = null){
        $this->checkEmailConfirmed();

        $template = cmsTemplate::getInstance();

        if ($this->request->has('submit')) {
            $this->submit($this->request->get('submit'));
        }

        $selectedTariff = false;

        $tariffPlans = $this->model->getActiveTariffPlans();
        $tariffsByPlanIds = [];
        $plansArr = [];
        foreach ($tariffPlans as $plan) {
            unset($plan['groups']);
            $plansArr[$plan['id']] = $plan;
            $tariffsByPlanIds[$plan['id']] = [];
        }

        $tariffsArr = $this->model->tariffs->fetchAllActive();
        $tariffs = [];
        /** @var paidaccessTariff $tariff */
        foreach ($tariffsArr as $tariff) {
            if (!($tariff->period ?? 0)) continue;

            if ($this->request->has('tariff_id')) {
                if ($this->request->get('tariff_id') == $tariff->id) {
                    $selectedTariff = $tariff;
                }
            }
            $a = $tariff->toArray();
            $tariffs[$tariff->id] = $a;
            $planId = $tariff->plan_id;
            if (!isset($plansArr[$planId])) {
                continue;
            }
            if (!isset($plansArr[$planId]['tariffs'])) {
                $plansArr[$planId]['tariffs'] = [];
            }
            $plansArr[$planId]['tariffs'][] = $a;
        }

        $plans=[];
        foreach ($plansArr as $plan) {
            if (isset($plan['tariffs'])) {
                $plans[$plan['id']] = $plan;
            }
        }
        
        return $template->render('buy', array(
            'plans' => $plans,
            'tariffs' => $tariffs,
            'selectedTariff' => $selectedTariff,
            'selectedPlan' => ($selectedTariff ? $selectedTariff['plan_id'] : $plan_id)
        ));
    }

    public function submit($type) {
        $tariffId = $this->request->get('tariff_id', false);
        if (!$tariffId) {
            cmsUser::addSessionMessage('Выберите тариф', 'error');
            return false;
        }
        $tariff = $this->model->tariffs->getById($tariffId);
        if (!$tariff || !$tariff->is_active) {
            cmsUser::addSessionMessage('Тариф не найден или не активен!', 'error');
            return false;
        }
        $plan = $tariff->plan;
        if (!$plan || !$plan->is_active) {
            cmsUser::addSessionMessage('Тарифный план не найден или не активен!', 'error');
            return false;
        }

        $order = $this->model->orders->findNotPaid($tariffId);
        if ($order) {
            $order->plan_id = $tariff->plan_id;
            $order->amount = $tariff->price;
        } else {
            $order = $this->model->orders->make($tariff);
        }

        if ($type == 'free') {
            if ($tariff->price != 0) {
                cmsUser::addSessionMessage('Указана неверная цена', 'error');
                return false;
            }

            $order->pay_type = 'free';
            $order->date_paid = now();
            $order->save();
            $order->activate();

        } elseif ($type == 'pay') {
            $order->pay_type = 'pay';
            $order->save();
            $invoice = $order->makeInvoice();
            $order->save();
            $this->redirectTo('pay', $invoice->id);
            
        } elseif ($type == 'bonus') {
            // TODO: check bonus code, check price,
            // TODO: if price is 0, add free order by bonuscode
            // TODO: if price is more than zero, create invoice, order and redirect to invoice pay
        }
        
        //$this->redirectTo('pay', 4);
    }

    

}
