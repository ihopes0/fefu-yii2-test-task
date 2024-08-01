<?php

namespace common\models;

use \yii\db\ActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int $created_at
 * @property int|null $updated_at
 * @property string $first_name
 * @property string $last_name
 * @property string $login
 * @property string $email
 * @property string $password
 * @property string|null $auth_key
 *
 * @property Meetup[] $meetups
 */
class User extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    public function behaviors()
    {
        return [
                TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'first_name', 'last_name', 'login', 'email', 'password'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['first_name', 'last_name', 'login', 'email', 'password'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
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
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'login' => 'Login',
            'email' => 'Email',
            'password' => 'Password',
            'auth_key' => 'Auth Key',
        ];
    }

    public function getMeetups()
    {
        return $this->hasMany(Meetup::class, ['id' => 'meetup_id'])->viaTable('user_meetup', ['user_id' => 'id']);
    }
}
