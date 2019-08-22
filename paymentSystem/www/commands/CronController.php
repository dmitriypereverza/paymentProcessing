<?php

namespace app\commands;

use app\components\DigitalDecrypt;
use app\components\DigitalEncrypt;
use app\components\RequestManager;
use app\models\Queue;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class CronController extends Controller {

    public function actionGenerateRequests() {

        $randomRequestCount = random_int(1, 10);
        for ($i = 0; $i <= $randomRequestCount; $i++) {
            $queueElement = new Queue();
            $queueElement->content = json_encode(Yii::$app->requestGeneratorService->generate());
            $queueElement->save();
        }
        return ExitCode::OK;
    }

    public function actionEncryptAndSendRequests() {
        /** @var Queue[] $queueItems */
        $queueItems = Queue::find()
            ->where(['success' => null ])
            ->andWhere([ 'inProgress' => false ])
            ->limit(10)
            ->all();
        Queue::setParamsInAll($queueItems, [ 'inProgress' => true ]);

        if (!$queueItems) {
            return;
        }
        $content = [];
        foreach ($queueItems as $queueItem) {
            $content[] = $this->getDigitalEncrypt()->encrypt($queueItem->content);
        }
        $content = json_encode($content);

        $response = $this->getRequestManager()->post('site/request', [
            'payload' => $content
        ]);
        if ($errorResponse = $this->getRequestManager()->getLastErrorResponse()) {
            Yii::error($errorResponse->getReasonPhrase());
        }

        if (!$response['success']) {
            echo 'Ошибка при запросе в эмулятор приема платежа';

            Queue::setParamsInAll($queueItems, [ 'success' => false, 'inProgress' => false ]);
            return;
        }
        Queue::setParamsInAll($queueItems, [ 'success' => true, 'inProgress' => false ]);
    }

    /**
     * @return DigitalEncrypt
     */
    private function getDigitalEncrypt(): DigitalEncrypt {
        return Yii::$app->encryptor;
    }
    /**
     * @return RequestManager
     */
    private function getRequestManager(): RequestManager {
        return Yii::$app->requestManager;
    }
}
