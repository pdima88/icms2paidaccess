<?php
namespace pdima88\icms2paidaccess\actions;

use cmsAction;
use cmsUser;
use cmsTemplate;
use pdima88\icms2paidaccess\frontend;
use pdima88\icms2paidaccess\model;
use pdima88\icms2bonuscode\model as modelBonuscode;

/**
 * @property model $model
 * @property modelPay $model_pay
 * @property modelBonuscode $model_bonuscode
 * @property pay $controller_pay
 * @property frontend $controller
 * @mixin frontend
 */
class buy extends cmsAction
{
    public function run($level = null){
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

        $plan_id = 0;
        $plans=[];
        foreach ($plansArr as $plan) {
            if (isset($plan['tariffs'])) {
                $plans[$plan['id']] = $plan;
                if (!$plan_id && $plan['level'] == $level) $plan_id = $plan['id'];
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
            $this->redirectToAction('checkout', $order->id);
        } elseif ($type == 'pay') {
            $order->pay_type = 'pay';
            $order->save();
            $this->redirectToAction('checkout', $order->id);
        } elseif ($type == 'bonus') {
            $code = $this->request->get('bonus', '');
            $res = $this->checkBonus($code, $tariffId);
            if (@$res['error']) {
                return false;
            }
            $totalAmount = $order->amount;
            if ($res['bonus']['type'] == 'discount_percent') {
                $discount = $res['bonus']['value'].'%';
                $totalAmount *= min(100, max(0, (100 - $res['bonus']['value']))) / 100;
            } else if ($res['bonus']['type'] == 'discount_value') {
                $discount = $res['bonus']['value'];
                $totalAmount = min($totalAmount,
                        max(0, $totalAmount - $res['bonus']['value']));
            } else {
                return false;
            }
            $order->bonuscode_id = $res['bonus']['id'];
            $order->discount = $discount;
            $order->total_amount = $totalAmount;
            $order->pay_type = 'pay';
            $order->save();

            $this->model_bonuscode->addActivation($order->bonuscode_id, cmsUser::getId(), [
                'product' => $plan->title. ' ('.$tariff->name.')',
                'product_id' => $tariffId,
                'typeid' => $this->name,
                'order_id' => $order->id,
            ]);

            if ($order->total_amount == 0) {
                $order->date_paid = now();
                $order->pay_type = 'bonus';
            }

            $order->save();

            $this->redirectToAction('checkout', $order->id);
        }

        //$this->redirectTo('pay', 4);
    }

    public function bonus($tariffId) {

    }



}
