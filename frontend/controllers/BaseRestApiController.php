<?php

namespace frontend\controllers;

use yii\rest\ActiveController;

class BaseRestApiController extends ActiveController
{

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'item',
    ];


    public function checkAccess($action, $model = null, $params = [])
    {
        return true;
    }

    public function behaviors()
    {
        return [
            'contentNegotiatior' => [
                'class' => \yii\filters\ContentNegotiator::class,
                'formatParam' => 'format',
                'formats' => [
                    'application/json' => \yii\web\Response::FORMAT_JSON,
                    'xml' => \yii\web\Response::FORMAT_XML,
                ],
            ],
        ];
    }

}
