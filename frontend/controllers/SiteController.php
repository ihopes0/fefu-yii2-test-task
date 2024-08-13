<?php

namespace frontend\controllers;

use yii\web\Controller;


/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * Отображет домашнюю страницу.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}