<?php
/**
 * @author Anton Tuyakhov <atuyakhov@gmail.com>
 */

namespace tuyakhov\jsonapi\tests\data;


class ActiveQuery extends \yii\db\ActiveQuery
{
    public static $models = [];

    public function one($db = null)
    {
        return isset(self::$models[0]) ? self::$models[0] : new $this->modelClass;
    }

    public function all($db = null)
    {
        return self::$models;
    }

    public function count($q = '*', $db = null)
    {
        return count(self::$models);
    }
}