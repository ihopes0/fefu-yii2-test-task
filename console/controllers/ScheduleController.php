<?php

namespace console\controllers;

use frontend\models\Meetup;
use frontend\models\User;
use frontend\models\UserMeetup;
use yii\console\Controller;

final class ScheduleController extends Controller
{
    public function actionCreateFor(array $usersId, int $day = null, bool $delete = false)
    {
        if (!$day || $day <= 0) {
            $day = date('d', time());
        }

        if ($delete) {
            UserMeetup::deleteAll(['user_id' => $usersId]);
        }

        if ($usersId[0] == 'all') {
            $users = User::find()->asArray()->all();
        } else {
            $users = User::find()
                ->where(['id' => $usersId])
                ->asArray()
                ->all();
        }
        if (!$users) {
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

        foreach ($users as $user) {
            $scheduledMeetups = [];
            $scheduledMeetupsId = [];
            $lastEndTime = 0;

            foreach ($meetups as $index => $meetup) {
                if (
                    $meetup['starts_at'] >= $lastEndTime &&
                    $meetup['count_participated_members'] < $meetup['max_number_of_members']
                ) {
                    $scheduledMeetups[] = $meetup;
                    $lastEndTime = $meetup['ends_at'];
                    $meetups[$index]['count_participated_members'] += 1;
                    $scheduledMeetupsId[] = $meetup['id'];
                }
            }

            foreach ($scheduledMeetups as $scheduledMeetup) {
                $userMeetup = new UserMeetup();
                $userMeetup->created_at = time();
                $userMeetup->user_id = $user['id'];
                $userMeetup->meetup_id = $scheduledMeetup['id'];
                if (!$userMeetup->save()) {
                    throw new \Exception('Failed to save meetup for user: ' . json_encode($userMeetup->errors));
                }
            }
            Meetup::updateAllCounters(['count_participated_members' => 1], ['id' => $scheduledMeetupsId]);
        }

        echo "Success!\n";

        return 0;
    }

    // for development only
    public function actionDeleteAllScheduledMeetups()
    {
        Meetup::updateAll(['count_participated_members' => 0]);
        UserMeetup::deleteAll();
    }
}
