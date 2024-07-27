<?php

namespace frontend\models;

final class Meetup extends \common\models\Meetup
{
    public function fields()
    {

        $fields = parent::fields();
        
        return $fields;
    }

    public function extraFields()
    {
        return [
            'users'
        ];
    }
}
