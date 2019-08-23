<?php

namespace app\commands;

use app\components\DigitalDecrypt;
use app\components\DigitalEncrypt;
use app\components\PaymentService;
use app\components\RequestManager;
use app\models\Queue;
use app\models\Transaction;
use app\models\UserWallet;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class CronController extends Controller {

    public function actionPaymentProcessing() {
        /** @var Queue $queueItem */
        $queueItem = Queue::find()
            ->where(['success' => null ])
            ->andWhere([ 'inProgress' => false ])
            ->one();

        if (!$queueItem) {
            return;
        }
        $queueItem->inProgress = true;
        $queueItem->save();

        if (!$data = json_decode($queueItem->content, true)) {
            $queueItem->inProgress = false;
            $queueItem->success = false;
            $queueItem->save();
            $str = 'Не удалось распарсить данные из очереди';
            Yii::error($str);
            echo $str;
            return;
        }

        try {
            $this->getPaymentService()->handle($data);
        } catch (\Exception $e) {
            $queueItem->inProgress = false;
            $queueItem->success = false;
            $queueItem->save();
            $str = 'Ошибка при регистрации платежа: ' . $e->getMessage();
            Yii::error($str);
            echo $str;
            return;
        }

        $queueItem->inProgress = false;
        $queueItem->success = true;
        $queueItem->save();
    }

    /**
     * @return PaymentService
     */
    private function getPaymentService(): PaymentService {
        return Yii::$app->paymentService;
    }
}
