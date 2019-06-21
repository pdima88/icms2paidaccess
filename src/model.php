<?php
namespace pdima88\icms2paidaccess;

use pdima88\icms2ext\Model as BaseModel;
use Exception;
use cmsModel;

/**
 * Class modelPaidaccess
 * @property tablePaidaccess_Orders $orders
 * @property tablePaidaccess_Tariffs $tariffs
 * @property tablePaidaccess_Plans $plans
 */
class model extends BaseModel {
    
    const TABLE_TARIFF_PLANS = 'paidaccess_plans';
    const TABLE_TARIFFS = 'paidaccess_tariffs';
    const TABLE_ORDERS = 'paidaccess_orders';
    const TABLE_PAIDACCESS = 'paidaccess';
    
    function __get($name)
    {
        if ($name == 'orders' || $name == 'tariffs' || $name == 'plans') {
            return $this->getTable($name);
        }
        throw new Exception('Unknown property '.$name);
    }

    function getTariffPlans() {
        $this->orderBy('sortorder', 'asc');
        return $this->get(self::TABLE_TARIFF_PLANS);
    }

    function getTariffPlan($id) {
        $tariffPlan = $this->getItemById(self::TABLE_TARIFF_PLANS, $id);
        if (!$tariffPlan) return null;

        $tariffPlan['groups'] = cmsModel::yamlToArray($tariffPlan['groups']);

        return $tariffPlan;
    }

    function getTariffPlansList($none = null, $noneTitle = null) {
        $res = array_column($this->getTariffPlans() ?: [], 'title', 'id');
        if (isset($none)) {
            $res = [$none => $noneTitle]+ $res;
        }
        return $res;
    }

    function getTariff($id) {
        return $this->getItemById(self::TABLE_TARIFFS, $id);
    }

    function updateTariff($id, $tariff) {
        $this->update(self::TABLE_TARIFFS, $id, $tariff);
    }

    function deleteTariff($id) {
        $this->delete(self::TABLE_TARIFFS, $id);
    }

    function getActiveTariffPlans() {
        $this->filter('is_active = 1');
        $this->orderBy('sortorder', 'asc');
        return $this->get(self::TABLE_TARIFF_PLANS);
    }

    function getTariffs() {
        $this->orderBy('plan_id', 'asc');
        $this->orderBy('period', 'asc');
        return $this->get(self::TABLE_TARIFFS);
    }

    function getActiveTariffs() {
        $this->filter('is_active = 1');
        return $this->getTariffs();
    }

    function isBonusTariff($bonusCodeTariffs, $tariffId) {
        $tariff = $this->getTariff($tariffId);
        if (!$tariff || !$tariff['is_active']) return false;
        $plan = $this->getTariffPlan($tariff['plan_id']);
        if (!$plan || !$plan['is_active']) return false;

        foreach ($bonusCodeTariffs as $t) {
            if ($t == 'all') return true;
            if (string_starts($t, 'p')) {
                $planId = substr($t, 1);
                if ($planId == $plan['id']) return true;
            }
            if ($t == $tariffId) return true;
        }
        return false;

    }

}
