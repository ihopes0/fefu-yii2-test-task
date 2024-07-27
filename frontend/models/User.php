<?php

namespace frontend\models;

use yii\helpers\Url;
use yii\web\Linkable;

final class User extends \common\models\User implements Linkable
{
    public function fields()
    {
        $fields = parent::fields();

        unset($fields['password'], $fields['auth_key']);

        return $fields;
    }

    public function extraFields()
    {
        return [
            'meetups'
        ];
    }

    public function getLinks()
    {
        return [
            'view' => Url::to(['user/view', 'id' => $this->id]),
        ];
    }
}
