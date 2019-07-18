<?php
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

