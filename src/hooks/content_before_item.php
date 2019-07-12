<?php
namespace pdima88\icms2paidaccess\hooks;

use cmsAction;
use cmsTemplate;
use cmsUser;

class content_before_item extends cmsAction {
    public function run($data) {
        list($ctype, $item, $fields) = $data;
        if (isset($item['is_paid']) && $item['is_paid']) {
            $itemLevel = $item['paidaccess_level'] ?? 0;
            $userLevel = cmsUser::isLogged() ? (cmsUser::get('paidaccess_level') ?? -1) : -1;

            if ($itemLevel > $userLevel) {   
                if (!isset($fields['teaser']) || !$fields['teaser']['is_in_item']) {
                    $fields['content']['html'] =  string_short($fields['content']['html'], 300, '...', 'w');
                } else {
                    $fields['content']['html'] = '';
                }
                $fields['content']['html'] .= cmsTemplate::getInstance()->getRenderedAsset('paidaccess', [
                    'itemLevel' => $itemLevel,
                    'userLevel' => $userLevel
                ]);
                $item['is_comments_on'] = 0;
                $ctype['is_comments'] = 0;
                unset($item['comments_widget']);
            }
        } 
        return [$ctype, $item, $fields];
    }
}