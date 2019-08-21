<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class CronController extends Controller {

    public function actionIndex() {
        $json = json_encode(Yii::$app->requestGeneratorService->generate());
        echo Yii::$app->digitalEncrypt->encrypt($json);

        return ExitCode::OK;
    }
}
