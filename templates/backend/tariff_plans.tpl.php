<?php
    $this->addJS('templates/default/js/jquery-ui.js');
    $this->addJS('templates/default/js/jquery-cookie.js');
    $this->addJS('templates/default/js/datatree.js');
    $this->addCSS('templates/default/css/datatree.css');
    $this->addJS('templates/default/js/admin-content.js');

	$this->setPageTitle('Тарифные планы');
	$this->addBreadcrumb('Подписки', $this->href_to('subscription'));
	$this->addBreadcrumb('Тарифные планы');

	$this->addToolButton([
		'class' => 'move',
		'title' => 'Назад к списку подписок',
		'href'  => $this->href_to('subscription')
	]);

	$this->addToolButton([
		'class' => 'view_list',
		'title' => 'Купленные подписки',
		'href'  => $this->href_to('subscription_list')
	]);

	$this->addToolButton([
		'class' => 'add',
		'title' => 'Добавить тарифный план',
		'href'  => $this->href_to('subscription_plan_add')
	]);

?>

<h2>Тарифные планы</h2>

<?php
$this->renderGrid($this->href_to('subscription_plan_ajax'), $grid);