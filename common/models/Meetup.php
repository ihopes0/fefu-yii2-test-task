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
            [['starts_at', 'ends_at', 'max_number_of_members', 'count_participated_members'], 'integer'],
        ];
    }

    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_meetup', ['meetup_id' => 'id']);
    }
}
