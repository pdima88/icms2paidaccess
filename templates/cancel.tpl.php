<?php
/**
 * @var cmsTemplate $this
 * @var \pdima88\icms2paidaccess\tables\row_order $order
 * @var bool $success
 * @var array $errors
 * @var cmsForm $form
 */ ?>

<h1>Отменить заказ</h1>

<div class="gui-panel">
    <h3>Заказ № <?= $order->id ?></h3>
    <p>

        Тариф: <?= $order->getInvoiceTitle() ?>
        <br>

        <?php if ($order->discount && $order->total_amount != $order->amount): ?>
            Цена: <?= format_currency($order->amount) ?><br>
            Скидка: <?= $order->discount ?><br>
            Цена с учетом скидки: <?= format_currency($order->total_amount) ?>
        <?php else: ?>
            Цена: <?= format_currency($order->total_amount) ?>
        <?php endif; ?><br>
        Дата создания: <?= format_datetime($order->date_created) ?>

    </p>
</div>

<?php if ($success): ?>

Заказ отменен

<?php else: ?>

    <?php html_errors($errors); ?>

    <?php if (!$errors && $form): ?>

    <p>Вы действительно хотите отменить заказ?</p>

    <?php $this->renderForm($form, [], [
        'submit' => [
            'title' => 'Да, отменить заказ',
        ],
        'cancel' => [
            'title' => 'Нет, вернуться к оплате',
            'href' => $this->href_to('checkout', [$order->id]),
            'show' => true,
        ],
    ]); ?>

    <?php endif; ?>
<?php endif; ?>