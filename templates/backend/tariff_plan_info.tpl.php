<h3><?= $item['title'] ?></h3>
<p><?= $item['hint'] ?></p>
<?php
    $first = true;
    foreach ($item['groups'] as $groupId) {
        if (isset($groups[$groupId])) {
            if ($first) {
                echo '<p>Группы пользователей, присваиваемые при подписке:</p><ul>';
            }
            echo '<li>'.$groups[$groupId].'</li>';
            $first = false;
        }
    }
    if (!$first) {
        echo '</ul>';
    }
?>