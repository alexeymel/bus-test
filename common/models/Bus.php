<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Class Bus
 * @package common\models
 *
 * @property integer $id
 * @property string $name
 * @property float $average_speed
 */
class Bus extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'average_speed'], 'required'],
            [['name'], 'string'],
            [['name'], 'unique'],
            [['average_speed'], 'double'],
        ];
    }
}