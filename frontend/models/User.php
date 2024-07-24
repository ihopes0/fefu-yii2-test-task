<?php

namespace frontend\models;

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
}
