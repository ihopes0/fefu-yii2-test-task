<?php

namespace frontend\models;

use common\models\Meetup;

final class User extends \common\models\User
{
    public function fields()
    {
        return [
            'id',
            'email',
            'name' => fn() => "{$this->first_name} {$this->last_name}",
            'login',
        ];
    }

    public function extraFields()
    {
        return [
            'meetups'
        ];
    }
}
