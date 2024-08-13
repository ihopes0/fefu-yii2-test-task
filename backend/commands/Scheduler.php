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
        ['usersId' => $usersId, 'date' => $date] = static::prepareData($usersId, $date);

        $users = static::findUsers($usersId);

        $meetups = static::findMeetups($date);

        echo sprintf("Making schedule for Users %s on %s\n", implode(',', $usersId), $date);
        static::makeSchedule($users, $meetups);

        return;
    }

    /**
     * Составляет расписание для пользователей $users из массива встреч $meetups
     */
    private static function makeSchedule(array $users, array $meetups): void
    {
        // проходит по каждому пользователю и создает ему расписание, потом сохраняет в БД
        foreach ($users as $user) {
            $scheduledMeetups = [];
            $lastMeetupEndTime = 0;

            foreach ($meetups as $index => $meetup) {
                // Если встреча начинается не раньше окончания предыдущей
                // и ее лимит участников не достигнут
                // встреча добавляется в массив назначенных встреч
                // и обновляется ее счетчик участников
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
                    echo sprintf('Failed to save meetup for user: %s\n', json_encode($userMeetup->errors) ?? 'no errors?');
                    continue;
                }

                $savedMeetupsId[] = $scheduledMeetup['id'];
            }
            Meetup::updateAllCounters(['count_participated_members' => 1], ['id' => $savedMeetupsId]);
        }

        return;
    }

    /**
     * Подготавливает данные с пользовательского ввода для получения пользователей и встреч с БД.
     * Значения передаются по ссылке, проходит проверка введеной даты.
     * В результате работы метода: 
     * - строка $usersId разбивается в массив id пользователей;
     * - дата приводится к формату y-m-d
     * 
     * @return array{usersId: string[], date: string}
     */
    private static function prepareData(string $usersId, string $date): array
    {
        $usersId = explode(',', $usersId);
        $usersId = static::filterInvalidUserId($usersId);

        if (empty($usersId)) {
            throw new \Exception('User validation error: not a single valid UserId is given');
        }
        if (!strtotime($date)) {
            throw new \Exception(sprintf("Date validation error: '%s' is not a valid date", $date));
        }

        $date = date('y-m-d', strtotime($date));

        return ['usersId' => $usersId, 'date' => $date];
    }

    /**
     * Получает массив пользователей по ID из БД
     */
    private static function findUsers(array $usersId): array
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
            throw new \Exception('Users not found');
        }

        return $users;
    }

    /**
     * Получает массив встреч по ID из БД
     */
    private static function findMeetups(string $date): array
    {
        $meetups = Meetup::find()
            ->where(['>=', 'starts_at', strtotime("{$date} 00:00")])
            ->andWhere(['<=', 'starts_at', strtotime("{$date} 23:59")])
            ->orderBy(['ends_at' => SORT_ASC])
            ->asArray()
            ->all();
        if (!$meetups) {
            throw new \Exception(sprintf('There are no meetings on %s', $date));
        }

        return $meetups;
    }

    /**
     * Проводит фильтрацию значений ID пользователей.
     * Пример: ['bili', 'bala', '1'] - - > ['1']
     * 
     * @return string[]
     */
    private static function filterInvalidUserId(array $usersId): array
    {
        $length = count($usersId);

        for ($i = 0; $i < $length; $i++) {
            $userId = $usersId[$i];
            if ($userId !== 'all' && (int) $userId === 0) {
                echo sprintf("%s is an invalid ID\n", $userId);
                unset($usersId[$i]);
            }
        }

        return array_values($usersId); // реиндексация массива
    }
}
