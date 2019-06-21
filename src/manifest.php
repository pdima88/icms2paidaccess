<?php
namespace pdima88\icms2paidaccess;

class manifest
{
    function hooks()
    {
        return [
            'bonuscode_component_list',
            'bonuscode_type_form',
        ];
    }
}
