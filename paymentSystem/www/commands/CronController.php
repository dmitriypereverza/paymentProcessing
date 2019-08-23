<?php

namespace app\commands;

use app\components\DigitalEncrypt;
use app\components\Pipe;
use app\components\RequestGeneratorComponent;
use app\components\RequestManager;
use app\models\Queue;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class CronController extends Controller {

    public function actionGenerateRequests($count = 10) {
        if ($count < 1) {
            return ExitCode::OK;
        }
        $randomRequestCount = random_int(1, $count);
        for ($i = 0; $i <= $randomRequestCount; $i++) {
            $data = $this->getPipeService()->create()
                ->addPipe([$this->getRequestGeneratorService(), 'generate'])
                ->addPipe(function ($data) { return json_encode($data); })
                ->exec();

            Queue::create($data);
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
            try {
                $content[] = $this->getDigitalEncrypt()->encrypt($queueItem->content);
            } catch (\Exception $e) {
                $queueItem->success = false;
                $queueItem->error = $e->getMessage();
                $queueItem->inProgress = false;
                $queueItem->save();
            }
        }

        $response = $this->getRequestManager()->post('site/request', [
            'payload' => json_encode($content)
        ]);
        if ($errorResponse = $this->getRequestManager()->getLastErrorResponse()) {
            Yii::error($errorResponse->getReasonPhrase());
            return;
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

    /**
     * @return RequestGeneratorComponent
     */
    private function getRequestGeneratorService(): RequestGeneratorComponent {
        return Yii::$app->requestGeneratorService;
    }

    /**
     * @return Pipe
     */
    private function getPipeService(): Pipe {
        return Yii::$app->pipe;
    }
}
