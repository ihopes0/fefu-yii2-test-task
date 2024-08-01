<?php

namespace frontend\controllers;

use backend\commands\Scheduler;
use frontend\models\Meetup;
use frontend\models\User;
use frontend\models\UserMeetup;
use Yii;

final class UserController extends BaseRestApiController
{
    public $modelClass = User::class;

    public function actionCreateSchedule(string $usersId, string $date)
    {
        Scheduler::make($usersId, $date);

        Yii::$app->response->statusCode = 200;
        Yii::$app->response->content = "OK";
        return Yii::$app->response->send();
    }
}
