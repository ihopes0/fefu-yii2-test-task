<?php

namespace frontend\models;

use yii\helpers\Url;
use yii\web\Linkable;

final class Meetup extends \common\models\Meetup implements Linkable
{
    public function fields()
    {

        $fields = parent::fields();
        $fields['pretty_date'] = fn($model) => date('y-m-d h:i', $model->starts_at);
        
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
