<?php
/**
 * @var cmsTemplate $this
 * @var \pdima88\icms2paidaccess\tables\row_order $order
 * @var \pdima88\icms2paidaccess\tables\row_tariff $tariff
 * @var \pdima88\icms2paidaccess\tables\row_plan $plan
 * @var string $submitText
 * @var \pdima88\icms2paidaccess\forms\form_userinfo $form
 */
use pdima88\icms2ext\Format;
use pdima88\icms2paidaccess\tables\table_orders; ?>

<h1>Активировать подписку</h1>

<?php if (!empty($errors)): ?>
<div class="sess_messages">
    <div class="message_error">
        Невозможно активировать подписку:
        <?php foreach ($errors as $err): ?>
            <br><?= $err ?>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>



<?php if ($order): ?>

<div class="gui-panel">
    <h3>Заказ № <?= $order->id ?></h3>
    <p>

        Тариф: <?php if ($plan): ?>
            <?= $plan->title ?>
            <?php endif; ?>
            <?php if ($tariff): ?>
                (<?= $tariff->name ?>)
            <?php else: ?>
                (<?= Format::formatDuration($order->period) ?>)
            <?php endif; ?>
            <br>

        <?php if ($order->discount && $order->total_amount != $order->amount): ?>
            Цена: <?= format_currency($order->amount) ?><br>
            Скидка: <?= $order->discount ?><br>
            Цена с учетом скидки: <?= format_currency($order->total_amount) ?>
        <?php else: ?>
            Цена: <?= format_currency($order->total_amount) ?>
        <?php endif; ?><br>
        Оплачено: <?= $order->date_paid ? format_datetime($order->date_paid) : 'не оплачено' ?><br>
        Тип оплаты: <?= table_orders::$payTypes[$order->pay_type] ?? $order->pay_type ?>
    </p>
</div>

<?php if ($form): ?>

<p>Активируйте подписку, чтобы получить доступ к разделам сайта</p>

<?php $this->renderForm($form, $user, [
    'submit' => [
        'title' => 'Активировать подписку',
    ],
]); ?>

<?php elseif ($order->date_activated): ?>

<p>Вы активировали подписку.
    Вам предоставлен доступ по тарифу <?= $plan ? $plan->title : '' ?> до <?= format_datetime($order->date_expiry) ?>
    <?php if ($order->questions): ?>
    и <?= $order->questions ?> вопросов, которые вы можете задать экспертам
    <?php endif; ?>
</p>

<?php elseif (!$order->date_paid): ?>
    <center>
        <a href="<?= $this->href_to('checkout', [$order->id]) ?>" class="button">Перейти к оплате</a>
        <a href="<?= $this->href_to('cancel', [$order->id]) ?>" class="button button-cancel">Отменить заказ</a>
    </center>

<?php endif; ?>

<?php endif; ?>
