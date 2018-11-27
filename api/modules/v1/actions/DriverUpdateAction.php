<?php

namespace api\modules\v1\actions;

use common\models\Bus;
use common\models\Driver;
use Yii;
use yii\rest\Action;

/**
 * Class DriverUpdateAction
 * @package api\modules\v1\actions
 */
class DriverUpdateAction extends Action
{
    /** @var array $errors */
    private $errors = [];

    /**
     * @param integer $id
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function run($id)
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        return $this->updateDriver($id);
    }

    /**
     * @param integer $id
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    protected function updateDriver($id)
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $driver = $this->getDriver($id, $requestParams);
        $buses = $this->getBuses($requestParams);

        if (!$this->errors) {
            $driver->save(false);
            $driver->unlinkAll('buses', false);
            /** @var Bus $bus */
            foreach ($buses as $bus) {
                $bus->save(false);
                $driver->link('buses', $bus);
            }
        }

        return [
            'status'    => boolval(!$this->errors),
            'errors'    => $this->errors,
            'data'      => $this->errors ? $requestParams : $driver,
        ];
    }

    /**
     * @param integer $id
     * @param array $requestParams
     * @return Driver|null
     */
    private function getDriver($id, $requestParams)
    {
        if ($driver = Driver::findOne($id)) {
            $driver->load($requestParams, '');
            if (!$driver->validate() || empty($requestParams['buses'])) {
                if (empty($requestParams['buses'])) {
                    $driver->addError('buses', 'Buses can not be blank');
                }
                $this->errors = array_merge($this->errors, $driver->getErrors());
            }
        } else {
            $this->errors['driver'] = ['Driver not found'];
        }

        return $driver;
    }

    /**
     * @param array $requestParams
     * @return Bus[]
     */
    private function getBuses($requestParams)
    {
        $buses = [];

        foreach ((array)@$requestParams['buses'] as $busData) {
            if (!$bus = Bus::findOne(['name' => @$busData['name']])) {
                $bus = new Bus();
            }
            $bus->load($busData, '');
            if (!$bus->validate()) {
                $this->errors = array_merge($this->errors, $bus->getErrors());
            }
            $buses[] = $bus;
        }

        return $buses;
    }
}