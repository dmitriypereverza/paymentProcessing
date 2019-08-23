<?php

namespace app\components;

use yii\base\Component;

class Pipe extends Component {

    private $data;
    private $isForeach = false;
    private $pipeline = [];

    public function create($data = null) {
        $this->data = $data;
        return $this;
    }

    public function foreach(array $data) {
        $this->data = $data;
        $this->isForeach = true;
        return $this;
    }

    public function addPipe($obj) {
        $this->pipeline[] = $obj;
        return $this;
    }

    private function execElement($data) {
        $data && $responseData = $data;
        foreach ($this->pipeline as $pipe) {
            $methodName = null;
            if (is_array($pipe) && isset($pipe[1])) {
                [$pipe, $methodName] = $pipe;
            }
            if (!$methodName) {
                $responseData = isset($responseData)
                    ? $pipe($responseData)
                    : $pipe();
                continue;
            }
            $responseData = isset($responseData)
                ? $pipe->{$methodName}($responseData)
                : $pipe->{$methodName}();
        }
        return $responseData;
    }

    public function exec() {
        if (!$this->isForeach) {
            return $this->execElement($this->data);
        }
        return array_map(function ($data) {
            return $this->execElement($data);
        }, $this->data);
    }
}
