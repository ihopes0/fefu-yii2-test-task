<?php

namespace console\controllers;

use frontend\models\Meetup;
use frontend\models\User;
use frontend\models\UserMeetup;
use yii\console\Controller;

final class ScheduleController extends Controller
{
    public function actionCreateFor(array $userId, int $day = null, bool $delete = false)
    {
        if (!$day || $day <= 0) {
            $day = date('d', time());
        }

        if ($userId[0] == 'all') {
            $this->createForAll($day, $delete);
            return;
        }

        $user = User::findOne(['id' => $userId]);
        if (!$user) {
            throw new \Exception('User not found');
        }

        $meetups = Meetup::find()
            ->where(['>=', 'starts_at', strtotime("24-10-{$day} 00:00")])
            ->andWhere(['<=', 'starts_at', strtotime("24-10-{$day} 23:59")])
            ->orderBy(['ends_at' => SORT_ASC])
            ->asArray()
            ->all();
        if (!$meetups) {
            throw new \Exception('Meetups not found');
        }

        $scheduledMeetups = [];
        $lastEndTime = 0;

        foreach ($meetups as $meetup) {
            if (
                $meetup['starts_at'] >= $lastEndTime &&
                $meetup['count_participated_members'] < $meetup['max_number_of_members']
            ) {
                $scheduledMeetups[] = $meetup;
                $lastEndTime = $meetup['ends_at'];
                $meetup['count_participated_members'] += 1;
                Meetup::updateAllCounters(['count_participated_members' => 1], ['id' => $meetup['id']]);
            }
        }

        if ($delete) {
            UserMeetup::deleteAll(['user_id' => $userId]);
        }

        foreach ($scheduledMeetups as $scheduledMeetup) {
            $userMeetup = new UserMeetup();
            $userMeetup->user_id = (int) $userId;
            $userMeetup->meetup_id = $scheduledMeetup['id'];
            if (!$userMeetup->save()) {
                throw new \Exception('Failed to save meetup for user: ' . json_encode($userMeetup->errors));
            }
        }
        echo "createFor {$user->first_name}\n";

        return 0;
    }

    private function createForAll(int $day, bool $delete)
    {
        echo "createForAll\n";
    }

    public function actionDeleteAllScheduledMeetups()
    {
        UserMeetup::deleteAll();
    }
}
