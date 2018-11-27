<?php

namespace api\modules\v1\actions;

use Yii;
use common\models\Driver;
use yii\data\ArrayDataProvider;
use yii\rest\Action;
use yii\web\NotFoundHttpException;

/**
 * Class MinimalTravelTime
 * @package api\modules\v1\actions
 */
class MinimalTravelTimeAction extends Action
{
    /**
     * @param null|integer $id
     * @param string $from
     * @param string $to
     * @return array
     * @throws NotFoundHttpException
     */
    public function run($id = null, $from, $to)
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        return $this->getMinimalTravelTime($id, $from, $to);
    }

    /**
     * @param null|integer $id
     * @param string $from
     * @param string $to
     * @return array
     * @throws NotFoundHttpException
     */
    protected function getMinimalTravelTime($id, $from, $to)
    {
        $minimalTravelTime = [];
        $distance   = $this->getDistance($from, $to);
        $drivers    = (array)$this->getDrivers($id);

        /** @var Driver $driver */
        foreach ($drivers as $driver) {
            $maxAverageSpeed = $driver->getMaxAverageSpeed();
            $travelTime = $this->getTravelTime($distance, $maxAverageSpeed);
            $minimalTravelTime[$driver->id] = $driver->getAttributes(['id', 'name', 'birth_date', 'age']);
            $minimalTravelTime[$driver->id]['travel_time'] = $travelTime;
        }

        return $id ? $minimalTravelTime[$id] : $this->getDataProvider($minimalTravelTime);
    }

    /**
     * Получаем провайдер данных для использования пагинации и сотрировки
     *
     * @param $dataArray
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    private function getDataProvider($dataArray)
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        return Yii::createObject([
            'class' => ArrayDataProvider::className(),
            'allModels' => $dataArray,
            'pagination' => [
                'params' => $requestParams,
            ],
            'sort' => [
                'defaultOrder' => [
                    'travel_time' => SORT_ASC,
                ],
                'attributes' => [
                    'travel_time',
                ],
            ],
        ]);
    }

    /**
     * Определяем расстояние между 2мя городами
     *
     * @param string $from
     * @param string $to
     * @return int
     * @throws NotFoundHttpException
     */
    private function getDistance($from, $to)
    {
        $distance = null;

        try {
            $query = http_build_query([
                'from' => $from,
                'to' => $to,
            ]);

            $content = file_get_contents(
                "https://www.avtodispetcher.ru/distance/export/frame?{$query}",
                true
            );

            if (preg_match('/Длина пути: .* км/u', $content, $matches)) {
                $stringDistance = @$matches[0];
                $distance = preg_replace('/\D/', '', $stringDistance);
            }
        } catch (\Throwable $exception) {
            // Не удалось получит данные
        }

        if (!$distance) {
            throw new NotFoundHttpException('Distance not found');
        }

        return (float)$distance;
    }

    /**
     * Ищем водителя по id
     *
     * @param integer $id
     * @return Driver[]
     * @throws NotFoundHttpException
     */
    private function getDrivers($id = null)
    {
        if ($id) {
            $drivers = [Driver::findOne($id)];
        } else {
            $drivers = Driver::find()->all();
        }

        if (!$drivers) {
            throw new NotFoundHttpException('Driver not found');
        }

        return $drivers;
    }

    /**
     * @param float $distance
     * @param float $speed
     * @return float
     */
    private function getTravelTime($distance, $speed)
    {
        $time = $speed ? $distance / $speed : 0;
        $days = $time / 8;

        return $days;
    }
}