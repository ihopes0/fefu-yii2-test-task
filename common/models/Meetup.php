<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Meetup model
 *
 * @property int $id
 * @property int $created_at
 * @property int|null $updated_at
 * @property string $title
 * @property int $starts_at
 * @property int $ends_at
 * @property string $place
 * @property int $max_number_of_members
 * @property int|null $count_participated_members
 *
 * @property User[] $users
 */
class Meetup extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%meetup}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'title', 'starts_at', 'ends_at', 'place', 'max_number_of_members'], 'required'],
            [['created_at', 'updated_at', 'starts_at', 'ends_at', 'max_number_of_members', 'count_participated_members'], 'integer'],
            [['title', 'place'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'title' => 'Title',
            'starts_at' => 'Starts At',
            'ends_at' => 'Ends At',
            'place' => 'Place',
            'max_number_of_members' => 'Max Number Of Members',
            'count_participated_members' => 'Count Participated Members',
        ];
    }

    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_meetup', ['meetup_id' => 'id']);
    }
}
