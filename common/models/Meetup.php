<?php

namespace common\models;

use frontend\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property string $date_start
 * @property string $date_end
 * @property integer $number_of_members
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
            ['date_start', 'string'],
            ['date_end', 'string'],
            ['number_of_members', 'integer'],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'date_start',
            'date_end',
            'room',
            'number_of_members'
        ];
    }

    public function extraFields()
    {
        return [
            'users'
        ];
    }

    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_meetup', ['meetup_id' => 'id']);
    }
}
