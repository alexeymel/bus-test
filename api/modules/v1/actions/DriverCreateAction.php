<?php

namespace api\modules\v1\actions;

use common\models\Bus;
use common\models\Driver;
use Yii;
use yii\rest\Action;

/**
 * Class DriverCreateAction
 * @package api\modules\v1\actions
 */
class DriverCreateAction extends Action
{
    /** @var array $errors */
    private $errors = [];

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        return $this->createDriver();
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    protected function createDriver()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $driver = $this->getDriver($requestParams);
        $buses = $this->getBuses($requestParams);

        if (!$this->errors) {
            $driver->save(false);
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
     * @param array $requestParams
     * @return Driver
     */
    private function getDriver($requestParams)
    {
        $driver = new Driver();
        $driver->load($requestParams, '');
        if (!$driver->validate() || empty($requestParams['buses'])) {
            if (empty($requestParams['buses'])) {
                $driver->addError('buses', 'Buses can not be blank');
            }
           $this->errors = array_merge($this->errors, $driver->getErrors());
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
            $bus = new Bus();
            $bus->load($busData, '');
            if (!$bus->validate()) {
                $this->errors = array_merge($this->errors, $bus->getErrors());
            }
            $buses[] = $bus;
        }

        return $buses;
    }
}