<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = 'Ошибка';
?>
<section class="content">

    <div class="error-page">
        <h2 class="headline text-info"><i class="fa fa-warning text-yellow"></i></h2>

        <div class="error-content">
            <h3>Ошибка #<?= $exception->statusCode ?></h3>

            <p>
                <?= nl2br(Html::encode($message)) ?>
            </p>

        </div>
    </div>

</section>
