<?php

namespace frontend\controllers;

use common\models\Meetup;
use yii\rest\ActiveController;

final class MeetupController extends ActiveController
{
    public $modelClass = Meetup::class;
}
