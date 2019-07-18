<?php
namespace pdima88\icms2paidaccess\hooks;

use cmsAction;
use cmsUser;
use cmsCore;
use cmsTemplate;
use pdima88\icms2paidaccess\frontend as paidaccess;

/**
 * @mixin paidaccess
 */
class user_tab_show extends cmsAction {

    public function run($profile, $tab_name){
        if ($tab_name == 'paidaccess') {
            
            $level = cmsUser::get('paidaccess_level');
            $expiry = cmsUser::get('paidaccess');
            $plan = null;
            if ($level) {
                $plan = $this->model->plans->getByLevel($level);
            }

            return $this->cms_template->renderInternal($this, 'profile_tab', [
                'profile' => $profile,
                'plan' => $plan,
                'expiry' => $expiry,
                'level' => $level
            ]);

        }        

    }

}
