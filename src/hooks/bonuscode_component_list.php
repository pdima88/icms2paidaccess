<?php
namespace pdima88\icms2paidaccess\hooks;

use cmsAction;

class bonuscode_component_list extends cmsAction {

    public function run($list){
        $list[$this->controller->name] = 'Платный доступ';
        return $list;
    }

}
