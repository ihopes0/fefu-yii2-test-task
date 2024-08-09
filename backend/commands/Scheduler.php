<?php

namespace backend\commands;

use common\models\Meetup;
use common\models\User;
use common\models\UserMeetup;

final class Scheduler
{
    public static function make(string $usersId, string $date)
    {
        static::prepareData($usersId, $date);

        $users = static::getUsers($usersId);

        $meetups = static::getMeetups($date);

        static::makeSchedule($users, $meetups);
    }


    private static function makeSchedule(array $users, array $meetups): void
    {
        foreach ($users as $user) {
            $scheduledMeetups = [];
            $lastEndTime = 0;

            foreach ($meetups as $index => $meetup) {
                if (
                    $meetup['starts_at'] >= $lastEndTime &&
                    $meetup['count_participated_members'] < $meetup['max_number_of_members']
                ) {
                    $scheduledMeetups[] = $meetup;
                    $lastEndTime = $meetup['ends_at'];

                    $meetups[$index]['count_participated_members'] += 1;
                }
            }

            $savedMeetupsId = [];

            foreach ($scheduledMeetups as $scheduledMeetup) {
                $userMeetup = new UserMeetup();
                $userMeetup->user_id = $user['id'];
                $userMeetup->meetup_id = $scheduledMeetup['id'];
                if (!$userMeetup->save()) {
                    echo 'Failed to save meetup for user: ' . json_encode($userMeetup->errors) . "\n";
                    continue;
                }
                $savedMeetupsId[] = $scheduledMeetup['id'];
            }
            Meetup::updateAllCounters(['count_participated_members' => 1], ['id' => $savedMeetupsId]);
        }
    }

    private static function prepareData(&$usersId, &$date)
    {
        $usersId = explode(',', $usersId);

        if (!strtotime($date)) {
            throw new \Exception('Date validation error: ' . '"' . $date . '"' . " is not a valid date");
        }
        $date = date('y-m-d', strtotime($date));
    }

    private static function getUsers($usersId)
    {
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

        return $users;
    }

    private static function getMeetups($date)
    {
        $result = Meetup::find()
            ->where(['>=', 'starts_at', strtotime("{$date} 00:00")])
            ->andWhere(['<=', 'starts_at', strtotime("{$date} 23:59")])
            ->orderBy(['ends_at' => SORT_ASC])
            ->asArray()
            ->all();
        if (!$result) {
            return "There are no meetings on {$date}\n";
        }

        return $result;
    }
}
