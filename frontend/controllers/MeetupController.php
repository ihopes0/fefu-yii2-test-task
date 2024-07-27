<?php

namespace frontend\controllers;

use frontend\models\Meetup;

final class MeetupController extends BaseRestApiController
{
    public $modelClass = Meetup::class;
}
