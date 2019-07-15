<?php /** @var cmsTemplate $this */ ?>
<h1>Активация демо-доступа</h1>

<?php if ($form): ?>

<p>
<?php if ($demoPeriod): ?>    
Активируйте демо-доступ, чтобы получить доступ к некоторым материалам сайта на 
<?= $demoPeriod ?> дней
<?php else: ?>
Активируйте демо-доступ, чтобы получить доступ к некоторым материалам сайта
<?php endif; ?>
</p>


<?php $this->renderForm($form, $data, [
        'submit' => [
            'title' => 'Активировать демо-доступ'
        ]
    ]); ?>

<?php else: ?>
<?php if ($success): ?>
        <?php if ($demoPeriod): ?>
            <p>
                Демо доступ активирован на <?= $demoPeriod ?> дней
            </p>
        <?php else: ?>
            <p>
                Демо доступ активирован
            </p>
        <?php endif; ?>
<?php else: ?>
<p>
Вы уже активировали демо-доступ
</p>
<?php endif; ?>

<?php endif; ?>
