<?php

namespace backend\commands;

use common\models\Meetup;
use common\models\User;
use common\models\UserMeetup;

final class Scheduler
{
    /**
     * @param string $usersId id пользователей через запятую без пробелов, для всех пользователей 'all' 
     * @param string $date формат y-m-d (24-10-01)
     */
    public static function make(string $usersId, string $date): void
    {
        static::prepareData($usersId, $date);

        $users = static::getUsers($usersId);

        $meetups = static::getMeetups($date);

        echo "Making schedule for Users " . implode(',', $usersId) . " on {$date}\n";
        static::makeSchedule($users, $meetups);
    }

    private static function makeSchedule(array $users, array $meetups): void
    {
        // проходит по каждому пользователю и создает ему расписание, потом сохраняет в БД
        foreach ($users as $user) {
            $scheduledMeetups = [];
            $lastMeetupEndTime = 0;

            foreach ($meetups as $index => $meetup) {
                // встреча начинается не раньше окончания предыдущей
                // и ее лимит участников не достигнут
                if (
                    $meetup['starts_at'] >= $lastMeetupEndTime &&
                    $meetup['count_participated_members'] < $meetup['max_number_of_members']
                ) {
                    $scheduledMeetups[] = $meetup;
                    $lastMeetupEndTime = $meetup['ends_at'];

                    $meetups[$index]['count_participated_members'] += 1;
                }
            }

            // массив id встреч, для которых обновляется счетчик присутствующих
            // после успешного сохранения расписания в БД
            $savedMeetupsId = [];

            // добавление записей о расписании в таблицу user_meetup
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

    /**
     * Подготавливает данные с пользовательского ввода для получения пользователей и встреч с БД.
     * Значения передаются по ссылке, проходит проверка введеной даты.
     * В результате работы метода: 
     * - строка $usersId разбивается в массив id пользователей;
     * - дата приводится к формату y-m-d
     */
    private static function prepareData(string &$usersId, string &$date): void
    {
        $usersId = explode(',', $usersId);
        $usersId = static::filterInvalidUserId($usersId);

        if (empty($usersId)) {
            throw new \Exception('User validation error: not a single valid UserId is given');
        }
        if (!strtotime($date)) {
            throw new \Exception('Date validation error: ' . '"' . $date . '"' . " is not a valid date");
        }

        $date = date('y-m-d', strtotime($date));
    }

    private static function getUsers(array $usersId): array
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
            throw new \Exception("There are no meetings on {$date}\n");
        }

        return $result;
    }

    private static function filterInvalidUserId(array $usersId): array
    {
        $length = count($usersId);
        for ($i = 0; $i < $length; $i++) {
            $userId = $usersId[$i];
            if ($userId !== 'all' && (int) $userId === 0) {
                echo "{$userId} is an invalid ID\n";
                unset($usersId[$i]);
            }
        }

        return array_values($usersId);
    }
}
