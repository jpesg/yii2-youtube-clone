<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $channel common\models\User */

use common\helpers\Html; ?>
<p>Hello <?= $channel->username ?></p>
<p>User <?php echo Html::channelLink($user, true) ?> has subscribed to you</p>

