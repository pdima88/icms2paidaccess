<?php
namespace pdima88\icms2paidaccess\hooks;

use cmsAction;
use cmsUser;
use pdima88\icms2paidaccess\model;

/**
 * @property model $model
 */
class user_tab_info extends cmsAction {

    public function run($profile, $tab_name){


        if($tab_name == 'paidaccess'){
            $count = $this->model->orders->selectAs()->columns(['id'])
                ->where('date_paid IS NOT NULL AND date_cancelled IS NULL AND date_activated IS NULL AND user_id = ?', cmsUser::getId())
                ->query()->rowCount();

            return array('counter'=> $count);

        }
    }

}
