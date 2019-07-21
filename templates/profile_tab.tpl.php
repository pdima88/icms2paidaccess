<?php
use pdima88\icms2paidaccess\tables\row_order;
use pdima88\icms2paidaccess\tables\table_orders;

/**
 * @var cmsTemplate $this
 * @var array $profile
 * @var \pdima88\icms2paidaccess\tables\row_plan $plan
 * @var int $expiry
 */
?>
<div class="gui-panel">
<h2>Ваш тариф:
<?php if (!isset($level)): ?>
    нет платного доступа. <a href="<?= $this->href_to('buy') ?>">Купить подписку</a>
<?php elseif ($level == 0): ?>
    демо-доступ. <a href="<?= $this->href_to('buy') ?>">Купить подписку</a>
<?php else: ?>
<?= $plan ? $plan->title : 'Тарифный план не найден' ?>
<?php endif; ?>
</h2>
    <p>

Срок действия до: <?= format_datetime($expiry) ?><br>
Количество оставшихся вопросов экспертам: <?= cmsUser::get('questions') ?>

    </p>
</div>

<?php if ($activateOrders->count() > 0): ?>
    <h3>У вас есть неактивированные заказы: <?= $activateOrders->count() ?></h3>

    <table>
        <thead>
            <tr>
                <th>Тариф</th>
                <th>Кол-во дней</th>
                <th>Кол-во вопросов</th>
                <th>Стоимость</th>
                <th>Дата оплаты</th>
                <th>Тип оплаты</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php /** @var row_order $order */ 
            foreach ($activateOrders as $order): ?>
            <tr>
                <td><?= $order->plan ? $order->plan->title : 'Тариф не найден' ?></td>
                <td><?= $order->period ?></td>
                <td><?= $order->questions ?></td>
                <td><?= $order->total_amount ?></td>
                <td><?= format_datetime($order->date_paid) ?></td>
                <td><?= table_orders::$payTypes[$order->pay_type] ?? $order->pay_type ?></td>
                <td><a href="<?= $this->href_to('activate', [$order->id]) ?>">Активировать</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>    
    </table>

<?php endif; ?>

