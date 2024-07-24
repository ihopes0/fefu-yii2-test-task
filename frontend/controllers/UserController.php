<?php

namespace frontend\controllers;

use frontend\models\User;
use yii\rest\ActiveController;

final class UserController extends ActiveController
{
    public $modelClass = User::class;
}
