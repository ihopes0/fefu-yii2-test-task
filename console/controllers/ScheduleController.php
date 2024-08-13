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
     * Создает расписание для указанных пользователей на указанную дату с помощью backend\commands\Scheduler
     */
    public function actionCreateFor(string $usersId, string $date): int
    {
        try {
            Scheduler::make($usersId, $date);
            echo "Success!\n";
            
        } catch (\Throwable $th) {
            echo sprintf('%s in %s on line %d', $th->getMessage(), $th->getFile(), $th->getLine());
        }

        return 0;
    }

    /**
     * Удаляет все записи из таблицы user_meetup и чистит счетчики участников на встречах в таблице meetup (удаляет расписание)
     */
    public function actionDeleteAllScheduledMeetups()
    {
        Meetup::updateAll(['count_participated_members' => 0]);
        UserMeetup::deleteAll();
    }
}
