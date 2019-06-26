<?php
namespace pdima88\icms2paidaccess\actions;

use cmsAction;
use cmsCore;
use cmsTemplate;
use cmsUser;
use Nette\Utils\Json;

/**
 * @property modelPaidaccess $model
 */
class bonus extends cmsAction
{
    const SESS_KEY = 'paidaccess_bonus_last_run';

    public function run()
    {
        $tpl = cmsTemplate::getInstance();
        if (!$this->request->isAjax()) cmsCore::error404();

        if (!cmsUser::isLogged()) {
            sendJson('reload');
        }

        $sessCheck = $_SESSION[self::SESS_KEY] ?? false;
        $_SESSION[self::SESS_KEY] = now();

        if ($sessCheck && ((time() - strtotime($sessCheck)) < 3)) {
            sendJsonError('not_found');
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
                        sendJson([
                            'result' => $res,
                            'bonus' => [
                                'value' => $bonusCode['bonus'] ?? 0,
                                'tariffs' => $bonusTariffs,
                                'type' => $bonusCode['type']['bonus_type'] ?? ''
                            ],                            
                        ]);
                    } else {
                        sendJsonError('activated');
                        // TODO: check can cancel activation
                    }
                } else {
                    sendJsonError('not_found');
                }
            } else {
                sendJsonError('not_found');
            }
        }
    }
}