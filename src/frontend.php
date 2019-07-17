<?php
namespace pdima88\icms2paidaccess;

use cmsFrontend;
use pdima88\icms2paidaccess\model as modelPaidaccess;
use cmsUser;

/** @property modelPaidaccess $model */
class frontend extends cmsFrontend {
   
    function checkEmailConfirmed($msg = null)
    {
        if (!cmsUser::isLogged()) cmsUser::goLogin();
        if (!cmsUser::getInstance()->get('email_confirmed')) {
            cmsUser::addSessionMessage($msg ?? 'Для приобретения подписки необходимо подтвердить ваш адрес электронной почты');
            $this->redirect(href_to('auth', 'verify').'?back='.urlencode($this->cms_core->uri));
        }
    }

    function checkBonus($code, $tariffId) {
        /** @var modelBonuscode $modelBonus */
        $modelBonus = $this->model_bonuscode;

        $bonusCode = $modelBonus->getByCode($code, $this->name);
        if ($bonusCode) {
            if ($modelBonus->checkActive($bonusCode)) {
                $act = $modelBonus->getActivation($bonusCode['id'], cmsUser::getId());
                if (!$act) {
                    $bonusTariffs = $bonusCode['type']['bonus_tariffs'] ?? [];
                    if ($tariffId && $this->model->isBonusTariff($bonusTariffs, $tariffId)) {
                        $res = 'ok';
                    } else {
                        $res = 'invalid_tariff';
                    }
                    return [
                        'result' => $res,
                        'bonus' => [
                            'id' => $bonusCode['id'],
                            'value' => $bonusCode['bonus'] ?? 0,
                            'tariffs' => $bonusTariffs,
                            'type' => $bonusCode['type']['bonus_type'] ?? ''
                        ],
                    ];
                } else {
                    return [
                        'error' => 'activated'
                    ];
                }
            }
        }
        return ['error' => 'not_found'];
    }
    
}
