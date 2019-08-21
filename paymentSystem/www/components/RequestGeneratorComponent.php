<?php

namespace app\components;

use yii\base\Component;

class RequestGeneratorComponent extends Component {
    public function generate ($content = null) {
        $randomFloat = function (int $start, int $end) {
            return random_int($start * 10, $end * 10) / 10;
        };
        return [
            'id' => uniqid(),
            'sum' => random_int(1, 500),
            'commision' => $randomFloat(0, 2),
            'order_number' => random_int(1, 20),
        ];
    }
}
