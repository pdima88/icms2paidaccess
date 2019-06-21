<?php
namespace pdima88\icms2paidaccess\actions;

use cmsAction;
use cmsCore;
use cmsUser;

/**
 * @property modelPaidaccess $model
 */
class bonus extends cmsAction
{
    const SESS_KEY = 'paidaccess_bonus_last_run';
    
    public function run()
    {
        if (!$this->request->isAjax()) cmsCore::error404();

        if (!cmsUser::isLogged()) {
            $this->json('reload');
        }

        $sessCheck = $_SESSION[self::SESS_KEY] ?? false;
        $_SESSION[self::SESS_KEY] = now();

        if ($sessCheck && ((time() - strtotime($sessCheck)) < 3)) {
            $this->jsonError('not_found');
        }

        $code = $this->request->get('code', '');
        $tariffId = $this->request->get('tariff_id', false);

        if ($code !== '') {

            /** @var modelBonuscode $modelBonuscode */
            $modelBonuscode = $this->model_bonuscode;

            $bonusCode = $modelBonuscode->getByCode($code, $this->name);
            if ($bonusCode) {
                if ($modelBonuscode->checkActive($bonusCode)) {
                    $act = $modelBonuscode->getActivation($bonusCode['id'], cmsUser::getInstance()->id);
                    if (!$act) {
                        $bonusTariffs = $bonusCode['type']['bonus_tariffs'] ?? [];
                        if ($tariffId && $this->model->isBonusTariff($bonusTariffs, $tariffId)) {
                            $res = 'ok';
                        } else {
                            $res = 'invalid_tariff';
                        }
                        $this->json([
                            'result' => $res,
                            'bonus' => [
                                'value' => $bonusCode['bonus'] ?? 0,
                                'tariffs' => $bonusTariffs,
                                'type' => $bonusCode['type']['bonus_type'] ?? ''
                            ],                            
                        ]);
                    } else {
                        $this->jsonError('activated');
                        // TODO: check can cancel activation
                    }
                } else {
                    $this->jsonError('not_found');
                }
            } else {
                $this->jsonError('not_found');
            }
        }
    }
}