<?php

namespace api\modules\v1\controllers;

use api\modules\base\controllers\ApiController;
use api\modules\v1\actions\DriverIndexAction;
use api\modules\v1\actions\DriverUpdateAction;
use api\modules\v1\actions\MinimalTravelTimeAction;
use api\modules\v1\actions\DriverCreateAction;

/**
 * Class DriverController
 * @package api\modules\v1\controllers
 *
 * Роуты:
 * GET  /driver - список всех водителей с привязанными к ним автобусами, с возможностью пагинации
 *      /driver?per_page=5&page=2
 *
 * POST /driver/create - добавляет объект водителя с набором автобусов которые он может водить
 *      {
 *          "name": "melnikov3",
 *          "birth_date": "14.04.1984",
 *          "buses": [
 *              {
 *                  "name": "busm1",
 *                  "average_speed": "60"
 *              },{
 *                  "name": "busn1",
 *                  "average_speed": "45"
 *              },{
 *                  "name": "busx1",
 *                  "average_speed": "75"
 *              }
 *          ]
 *      }
 *
 * PATCH /driver/update - обновляет объект водителя и набор автобусов которые он может водить (обновляет автобусы,
 * либо создает новые если отсутствуют)
 *      {
 *          "name": "melnikov3",
 *          "birth_date": "14.04.1984",
 *          "buses": [
 *              {
 *                  "name": "busm1",
 *                  "average_speed": "60"
 *              },{
 *                  "name": "busn1",
 *                  "average_speed": "45"
 *              },{
 *                  "name": "busx1",
 *                  "average_speed": "75"
 *              }
 *          ]
 *      }
 *
 * GET  /driver/view?id=2 - просмотр объекта водителя с привязанными к нему автобусами по id
 *
 * GET  /driver/time?from=Москва&to=Иркутск - расчет минимального времени прохождения маршрута по всем водителям
 * с возможностью пагинации
 *      /driver/time?from=Москва&to=Иркутск&per_page=5&page=2
 *
 * GET  /driver/time?from=Москва&to=Иркутск&id=4 - расчет минимального времени прохождения маршрута для конкретного
 * водителя по id
 *
 * .....
 */
class DriverController extends ApiController
{
    /** @var string $modelClass */
    public $modelClass = 'common\models\Driver';

    /**
     * @return array
     */
    public function actions()
    {
        $actions = [
            'time' => [
                'class' => MinimalTravelTimeAction::className(),
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'index' => [
                'class' => DriverIndexAction::className(),
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => DriverCreateAction::className(),
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'update' => [
                'class' => DriverUpdateAction::className(),
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ];

        return array_merge(parent::actions(), $actions);
    }

    /**
     * @return array
     */
    public function verbs()
    {
        $verbs = [
            'time' => ['GET'],
        ];

        return array_merge(parent::verbs(), $verbs);
    }
}