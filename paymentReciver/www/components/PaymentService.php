<?php

namespace app\components;

use app\models\Transaction;
use app\models\UserWallet;
use yii\base\Component;
use yii\base\Exception;

class PaymentService extends Component {

    public function handle($data) {
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
    }
}
