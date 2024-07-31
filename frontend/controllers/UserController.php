<?php

namespace frontend\controllers;

use frontend\models\Meetup;
use frontend\models\User;
use frontend\models\UserMeetup;
use Yii;

final class UserController extends BaseRestApiController
{
    public $modelClass = User::class;

    public function actionCreateSchedule(string $usersId, string $date)
    {
        $usersId = explode(',', $usersId);

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
            return "There are no meetings on {$date}\n";
        }

        $this->makeSchedule($users, $meetups);

        Yii::$app->response->statusCode = 200;
        Yii::$app->response->content = "OK";
        return Yii::$app->response->send();
    }


    private function makeSchedule(array $users, array $meetups): void
    {
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
    }
}
