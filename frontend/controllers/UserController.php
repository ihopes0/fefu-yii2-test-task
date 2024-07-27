<?php

namespace frontend\controllers;

use frontend\models\User;

final class UserController extends BaseRestApiController
{
    public $modelClass = User::class;
}
