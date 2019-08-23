<?php

namespace app\controllers;

use app\components\DigitalDecrypt;
use app\components\Pipe;
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

        try {
            $this->getPipe()->foreach($data)
                ->addPipe([$this->getDigitalDecrypt(), 'decrypt'])
                ->addPipe(function ($data) {
                    Queue::create($data);
                })
                ->exec();
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'payload' => null,
            ]);
        }

        return json_encode([
            'success' => true,
            'error' => null,
            'payload' => null,
        ]);
    }

    private function getDigitalDecrypt(): DigitalDecrypt {
        return Yii::$app->decryptor;
    }

    private function getPipe(): Pipe {
        return Yii::$app->pipe;
    }
}
