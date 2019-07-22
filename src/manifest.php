<?php
namespace pdima88\icms2paidaccess;

class manifest
{
    function hooks()
    {
        return [
            'bonuscode_component_list',
            'bonuscode_type_form',
            'content_before_item',
            'user_tab_info',
            'user_tab_show',
            'pay_invoice_set_paid',
        ];
    }

    function getRootPath() {
        return realpath(dirname(__FILE__).'/..');
    }
}
