<?php
namespace pdima88\icms2paidaccess\actions;

use cmsAction;
use cmsCore;
use cmsTemplate;
use cmsUser;
use Nette\Utils\Json;
use pdima88\icms2paidaccess\frontend;

/**
 * @property modelPaidaccess $model
 * @mixin frontend
 */
class bonus extends cmsAction
{
    const SESS_KEY = 'paidaccess_bonus_last_run';

    public function run()
    {
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
            sendJson($this->checkBonus($code, $tariffId));
        }
    }
}