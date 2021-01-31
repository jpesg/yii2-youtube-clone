<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Video */

$this->title = 'Create Video';
$this->params['breadcrumbs'][] = ['label' => 'Videos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="video-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="d-flex flex-column align-items-center justify-content-center">
        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data']
        ]) ?>

        <div class="upload-icon">
            <i class="fas fa-upload"></i>
        </div>
        <br>
        <p class="m-0">Drag and drop a file you want to upload</p>
        <p class="text-muted">Your video will be private until you publish it</p>

        <?php $form->errorSummary($model) ?>

        <button class="btn btn-primary btn-file">
            Select file
            <input type="file" id="videoFile" name="video">
        </button>
        <?php ActiveForm::end() ?>
    </div>

</div>
