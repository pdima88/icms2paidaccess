<?php
namespace pdima88\icms2paidaccess\backend\forms;

use cmsForm;
use fieldCheckbox;
use fieldList;
use fieldNumber;
use pdima88\icms2paidaccess\frontend as paidaccess;

/**
 * @property paidaccess $controller
 */
class form_tariff extends cmsForm {
	
	public function init(){
		$plans = $this->controller->model->getTariffPlansList(false, '(Выберите из списка)');

		return array(
			array(
				'type' => 'fieldset',
				'title' => 'Тариф на платную подписку',
				'childs' => array(
					
					new fieldCheckbox('is_active', array(
						'title' => 'Активна',
						'default' => true,
					)),

					new fieldList('plan_id', array(
						'title' => 'Тарифный план',
						'items' => $plans,
						'rules' => array(
							array('required'),
						)
					)),
					
					new fieldNumber('period', array(
						'title' => 'Длительность подписки (дней)',
						'default' => 7,
						'rules' => array(
							array('required'),
						)
					)),

					new fieldNumber('question', array(
						'title' => 'Количество бонусных вопросов',
						'default' => 0,
					)),
					
					new fieldNumber('price', array(
						'title' => 'Стоимость',
						'default' => 100,
						'rules' => array(
							array('required'),
						)
					)),
 					
				)
				
			),
			
		);
		
	}

	public function parse($request, $is_submitted=false, $item=false){
		$result = parent::parse($request, $is_submitted, $item);
		if (!isset($result['is_active'])) $result['is_active'] = false;
		return $result;
	}
	
}
