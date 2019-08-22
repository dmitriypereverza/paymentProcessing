<?php

namespace app\controllers;

use app\components\DigitalDecrypt;
use app\models\Queue;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

class SiteController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    '*'   => ['post']
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionRequest() {
        $data = Yii::$app->request->post('payload');
        $data = json_decode($data, true);

        foreach ($data as $item) {
            $decryptedText = $this->getDigitalDecrypt()->decrypt($item);
            $queueElement = new Queue();
            $queueElement->content = $decryptedText;
            $queueElement->save();
        }

        return json_encode([
            'success' => true,
            'payload' => 'dfdfsdsdf',
        ]);
    }

    private function getDigitalDecrypt(): DigitalDecrypt {
        return Yii::$app->decryptor;
    }
}
