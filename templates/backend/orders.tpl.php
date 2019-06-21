<?php
use pdima88\phpassets\Assets;
/** @var cmsTemplate $this */
$this->addJS('templates/default/js/jquery-cookie.js');
$this->addJS('templates/default/js/datatree.js');
$this->addCSS('templates/default/css/datatree.css');
$this->addJS('templates/default/js/admin-content.js');

$this->setPageTitle('Заказы');

$this->addBreadcrumb('Заказы', $this->href_to('orders'));

/** @var \s4y\grid\Grid $grid */
$this->addToolButton(array(
    'class' => 'excel',
    'title' => 'Экспорт',
    'href'  => $grid->appendSortAndFilterParams($this->href_to('orders').'?export=csv'),
    'target' => '_blank',
));

?>

<script type="text/javascript">
    $(function(){
        $('.cp_toolbar .excel a').addClass('s4y-grid-orders-export')
            .attr('data-url', $.pdgrid.appendUrlParams($('#s4y_grid_orders').attr('data-url'), {export:'csv'}));
    });
</script>
<style>
    .cp_toolbar {
        float: right;
        margin-top: -50px;
    }
</style>


<div class="cp_toolbar">
    <?php /** @var cmsTemplate $this */
    $this->toolbar(); ?>
</div>
<?php

/** @var pdgrid\Grid $grid */
$gridStr = $grid->render();
Assets::addStyle('display:none', '.pdgrid-action-btn');

$this->addHead(Assets::getCss());
$this->addOutput(Assets::getJs());

echo $gridStr;
?>



