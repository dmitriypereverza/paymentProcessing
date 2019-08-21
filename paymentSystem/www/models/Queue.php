<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class Queue
 *
 * @property string $content
 * @property boolean $success
 * @property boolean $inProgress
 *
 * @package app\models
 */
class Queue extends ActiveRecord {


    /**
     * @param Queue[] $queueItems
     *
     * @return int
     */
    public static function setParamsInAll(array $queueItems, $params) {
        $ids = array_map(function (Queue $item) {
            return $item->id;
        }, $queueItems);

        return Queue::updateAll($params, [ 'in', 'id', $ids ]);
    }
}
