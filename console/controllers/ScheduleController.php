<?php

namespace console\controllers;

use frontend\models\Meetup;
use frontend\models\User;
use frontend\models\UserMeetup;
use yii\console\Controller;

/**
 * Schedule console controller
 */
final class ScheduleController extends Controller
{

    /**
     * Creates a schedule for given users using a simple approximate greedy algorithm.
     * @return int 0 if successful
     * @throws \Exception on any error
     */
    public function actionCreateFor(array $usersId, string $date): int
    {
        if (!strtotime($date)) {
            throw new \Exception('Date validation error: ' . '"' . $date . '"' . " is not a valid date");
        }
        $date = date('y-m-d', strtotime($date));

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
            ->where(['>=', 'starts_at', strtotime("{$date} 00:00")])
            ->andWhere(['<=', 'starts_at', strtotime("{$date} 23:59")])
            ->orderBy(['ends_at' => SORT_ASC])
            ->asArray()
            ->all();
        if (!$meetups) {
            echo "There are no meetings on {$date}\n";
            return 1;
        }

        echo "Making schedule for Users " . implode(',', $usersId) . " on {$date}\n";

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
