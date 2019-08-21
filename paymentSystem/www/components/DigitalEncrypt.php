<?php

namespace app\components;

use yii\base\Component;
use yii\base\Exception;

class DigitalEncrypt extends Component {

    public $publicKeyPath;
    public $publicKey;

    public function init() {
        parent::init();

        $realpath = \Yii::$app->basePath . '/' . $this->publicKeyPath;
        if (!file_exists($realpath)) {
            throw new Exception(sprintf('Не существует файла %s', $realpath));
        }
        if (!$this->publicKey = file_get_contents($realpath)) {
            throw new Exception('Не удалось получить публичный ключ из файла');
        }
    }

    public function encrypt (string $text): string {
        if (!$publicKey = openssl_pkey_get_public($this->publicKey)) {
            throw new Exception('Не удалось получить публичный ключ');
        }
        if (!openssl_public_encrypt($text,$encryptedWithPublic, $publicKey)) {
            throw new Exception('Ошибка при шифровании публичным ключом');
        }
        return base64_encode($encryptedWithPublic);
    }
}
