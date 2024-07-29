<?php

namespace common\models;

use yii\db\ActiveRecord;

class UserMeetup extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_meetup';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'meetup_id'], 'integer'],
        ];
    }
}
