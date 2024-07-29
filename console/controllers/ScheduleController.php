<?php

namespace console\controllers;

use yii\console\Controller;

final class ScheduleController extends Controller
{
    public function actionCreateFor(array $userId, int $day, bool $delete = false)
    {
        if($userId[0] == 0) {
            $this->createForAll($userId, $day, $delete);
            return;
        }
        echo "createFor\n";
    }

    private function createForAll(array $userId, int $day, bool $delete)
    {
        echo "createForAll\n";
    }
}
