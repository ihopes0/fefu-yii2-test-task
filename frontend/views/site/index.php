<?php

/** @var yii\web\View $this */

$this->title = 'Главная';
?>
<div class="site-index">
    <div class="p-5 mb-4 bg-transparent rounded-3">
        <div class="container-fluid py-5 text-center">
            <h1 class="display-4">Тестовое задание по Yii2 - fefu</h1>
            <p class="fs-5 fw-light"></p>
        </div>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h4>Чтобы посмотреть пользователей</h4>

                <p>/user</p>

                <h4>с расписанием</h4>

                <p>/user?expand=meetups</p>

            </div>
            <div class="col-lg-4">
                <h4>Чтобы посмотреть встречи</h4>

                <p>/meetup</p>

                <h4>с участниками</h4>

                <p>/meetup?expand=users</p>

            </div>
            <div class="col-lg-4">
                <h4>Чтобы составить расписание пользователю</h4>

                <p>/user/create-schedule?id=&day=</p>
                <p>
                    <span>где id - id пользователя или пользователей (через запятую), для всех пользователей id=all;</span>
                    <br>
                    <span>day - дата, на которую нужно составить расписание, формат y-m-d</span>

                </p>

            </div>
        </div>

    </div>
</div>
