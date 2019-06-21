<?php
namespace pdima88\icms2paidaccess;

use cmsBackend;

class backend extends cmsBackend {
   
    public $useDefaultOptionsAction = true;

    public function actionIndex(){
        $this->redirectToAction('orders');
    }

    public function getBackendMenu(){
        return array(
            array(
                'title' => 'Заказы',
                'url' => href_to($this->root_url, 'orders')
            ),
            array(
                'title' => 'Тарифные планы',
                'url' => href_to($this->root_url, 'tariffs')
            ),
            array(
                'title' => 'Демо-доступ',
                'url' => href_to($this->root_url, 'demo')
            ),
            array(
                'title' => 'Настройки',
                'url' => href_to($this->root_url, 'options')
            ),

            
        );
    }

    protected function validateParamsCount($class, $method_name, $params)
    {
        return true;
    }

}
