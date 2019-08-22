<?php

namespace app\components;

use yii\base\Component;
use yii\base\Exception;

class DigitalDecrypt extends Component {

    public $privateKeyPath;
    public $privateKey;

    public function init() {
        parent::init();

        $realpath = \Yii::$app->basePath . '/' . $this->privateKeyPath;
        if (!file_exists($realpath)) {
            throw new Exception(sprintf('Не существует файла %s', $realpath));
        }
        if (!$this->privateKey = file_get_contents($realpath)) {
            throw new Exception('Не удалось получить приватный ключ из файла');
        }
    }

    public function decrypt (string $text): string {
        $text = base64_decode($text);
        if (!$privateKey = openssl_pkey_get_private($this->privateKey)) {
            throw new Exception('Не удалось получить приватный ключ');
        }
        if (!openssl_private_decrypt($text,$encryptedWithPublic, $privateKey)) {
            throw new Exception('Ошибка при дешифровании приватным ключом');
        }
        return $encryptedWithPublic;
    }
}
