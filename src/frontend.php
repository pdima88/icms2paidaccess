<?php
namespace pdima88\icms2paidaccess;

use cmsFrontend;
use pdima88\icms2paidaccess\model as modelPaidaccess;
use cmsUser;

/** @property modelPaidaccess $model */
class frontend extends cmsFrontend {
   
    function checkEmailConfirmed() 
    {
        if (!cmsUser::isLogged()) cmsUser::goLogin();
        if (!cmsUser::getInstance()->get('email_confirmed')) {
            cmsUser::addSessionMessage('Для приобретения подписки необходимо подтвердить ваш адрес электронной почты');            
            $this->redirect(href_to('auth', 'verify').'?back='.urlencode($this->cms_core->uri));
        }
    }
    
}
