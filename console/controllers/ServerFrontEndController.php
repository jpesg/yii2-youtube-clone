<?php

namespace console\controllers;

class ServerFrontEndController extends \yii\console\controllers\ServeController
{
    public $docroot = '@frontend/web';

    public function actionIndex($address = '127.0.0.1')
    {
        return parent::actionIndex($address);
    }
}
