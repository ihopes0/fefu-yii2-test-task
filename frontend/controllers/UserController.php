<?php

namespace frontend\controllers;

use backend\commands\Scheduler;
use frontend\models\User;
use Yii;

final class UserController extends BaseRestApiController
{
    public $modelClass = User::class;

    public function actionCreateSchedule(string $usersId, string $date)
    {

        try {
            Scheduler::make($usersId, $date);

            Yii::$app->response->statusCode = 200;
            Yii::$app->response->content = 'OK';
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->content = $th->getMessage();
        }

        return Yii::$app->response->send();
    }
}
