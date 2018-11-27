<?php

namespace api\modules\v1\controllers;

use api\modules\base\controllers\ApiController;

/**
 * Class BusController
 * @package api\modules\v1\controllers
 *
 * Роуты:
 * GET  /bus - список всех автобусов с возможностью пагинации
 *      /bus&per_page=5&page=2
 *
 * POST /bus/create - добавляет объект автобуса
 *      {
 *          "name": "busm1",
 *          "average_speed": "60"
 *      }
 *
 * GET  /bus/view?id=2 - просмотр объекта автобуса по id
 *
 * .....
 */
class BusController extends ApiController
{
    /** @var string $modelClass */
    public $modelClass = 'common\models\Bus';
}