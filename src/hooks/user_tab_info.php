<?php
namespace pdima88\icms2paidaccess\hooks;

use cmsAction;

class user_tab_info extends cmsAction {

    public function run($profile, $tab_name){


        if($tab_name == 'paidaccess'){



            return array('counter'=> 0);

        }
    }

}
