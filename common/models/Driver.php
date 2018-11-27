<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Class Driver
 * @package common\models
 *
 * @property integer $id
 * @property string $name
 * @property string $birth_date
 * @property integer $age
 * @property array $buses
 */
class Driver extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'driver';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'birth_date'], 'required'],
            [['name'], 'string'],
            [['name'], 'unique'],
            [['birth_date'], 'date', 'format' => 'php:d.m.Y'],
        ];
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'birth_date',
            'age',
            'buses',
        ];
    }

    /**
     * Вычисляем возраст водителя
     *
     * @return int
     */
    public function getAge()
    {
        $birthDate = new \DateTime($this->birth_date);

        return $birthDate->diff(new \DateTime())->y;
    }

    /**
     * Получаем список автобусов водителя
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBuses()
    {
        $tableName = Driver::tableName() . '_' . Bus::tableName();

        return $this->hasMany(Bus::classname(), ['id' => 'bus_id'])
            ->viaTable($tableName, ['driver_id' => 'id']);
    }

    /**
     * @return float
     */
    public function getMaxAverageSpeed()
    {
        $maxAverageSpeed = 0;

        /** @var Bus $bus */
        foreach ($this->buses as $bus) {
            if ($bus->average_speed > $maxAverageSpeed) {
                $maxAverageSpeed = $bus->average_speed;
            }
        }

        return $maxAverageSpeed;
    }
}