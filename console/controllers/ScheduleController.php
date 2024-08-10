<?php

namespace console\controllers;

use frontend\models\Meetup;
use frontend\models\UserMeetup;
use backend\commands\Scheduler;
use yii\console\Controller;

/**
 * Schedule console controller
 */
final class ScheduleController extends Controller
{

    /**
     * Creates a schedule via Scheduler class.
     * @return int 0 if successful
     */
    public function actionCreateFor(string $usersId, string $date): int
    {
        try {
            Scheduler::make($usersId, $date);
            echo "Success!\n";
            
        } catch (\Throwable $th) {
            echo $th->getMessage() . " in " . $th->getFile() . " on line " . $th->getLine();
        }

        return 0;
    }

    // for development only
    public function actionDeleteAllScheduledMeetups()
    {
        Meetup::updateAll(['count_participated_members' => 0]);
        UserMeetup::deleteAll();
    }
}
