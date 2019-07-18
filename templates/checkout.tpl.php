<?php
/**
 * @var cmsTemplate $this
 * @var \pdima88\icms2paidaccess\tables\row_order $order
 * @var \pdima88\icms2paidaccess\tables\row_tariff $tariff
 * @var \pdima88\icms2paidaccess\tables\row_plan $plan
 * @var string $submitText
 * @var \pdima88\icms2paidaccess\forms\form_userinfo $form
 */ ?>

<h1>Приобрести подписку</h1>

<div class="gui-panel">
    <h3>Выбран тариф: <?= $plan->title ?> (<?= $tariff->name ?>)</h3>
    <div class="paidaccess-tariffplan-selected">
        <?php if ($order->discount && $order->total_amount != $order->amount): ?>
        Цена: <?= format_currency($order->amount) ?><br>
        Скидка: <?= $order->discount ?><br>
        Цена с учетом скидки: <?= format_currency($order->total_amount) ?>
        <?php else: ?>
        Цена: <?= format_currency($order->total_amount) ?>
        <?php endif; ?>
    </div>
</div>

<?php $this->renderForm($form, $user, [
    'submit' => [
        'title' => ($order->total_amount == 0 || $order->date_paid) ? 'Активировать подписку' : false,
        'show' => !$form->hasField('pay_type')
    ],
    'cancel' => [
        'title' => 'Отменить заказ',
        'href' => $this->href_to('cancel', [$order->id]),
        'show' => true,
    ]
]); ?>


