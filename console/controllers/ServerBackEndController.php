<?php

namespace console\controllers;

class ServerBackEndController extends \yii\console\controllers\ServeController
{
    public $docroot = '@backend/web';
    public $port = 4500;

    public function actionIndex($address = '127.0.0.1')
    {
        return parent::actionIndex($address);
    }
}
