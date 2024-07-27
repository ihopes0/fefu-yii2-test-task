<?php

namespace frontend\controllers;

use common\models\Meetup;

final class MeetupController extends BaseRestApiController
{
    public $modelClass = Meetup::class;
}
