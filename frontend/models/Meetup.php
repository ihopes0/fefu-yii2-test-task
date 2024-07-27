<?php

namespace frontend\models;

use yii\helpers\Url;
use yii\web\Linkable;

final class Meetup extends \common\models\Meetup implements Linkable
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

    public function getLinks()
    {
        return [
            'view' => Url::to(['meetup/view', 'id' => $this->id]),
        ];
    }
}
