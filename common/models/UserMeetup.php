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
            [['created_at', 'user_id', 'meetup_id'], 'integer'],
            [['user_id', 'meetup_id'], 'required'],
            [['meetup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meetup::class, 'targetAttribute' => ['meetup_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'user_id' => 'User ID',
            'meetup_id' => 'Meetup ID',
        ];
    }
}
