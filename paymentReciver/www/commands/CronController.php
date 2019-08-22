<?php

namespace app\commands;

use app\components\DigitalDecrypt;
use app\components\DigitalEncrypt;
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
            Yii::error('Не удалось распарсить данные из очереди');
        }

        $transaction = new Transaction();
        $transaction->id = $data['id'];
        $transaction->user_id = $data['order_number'];
        $transaction->sum = $data['sum'] - ((float)$data['sum'] * (float)$data['commision'] / 100);
        $transaction->save();

        $userWallet = UserWallet::find()
            ->where(['user_id' => $transaction->user_id ])
            ->one();

        if (!$userWallet) {
            $userWallet = new UserWallet();
            $userWallet->user_id = $transaction->user_id;
        }

        $userWallet->sum += $transaction->sum;
        $userWallet->save();

        $queueItem->inProgress = false;
        $queueItem->success = true;
        $queueItem->save();
    }
}
