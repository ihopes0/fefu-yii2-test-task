<?php

use yii\db\Migration;

/**
 * Class m240725_072303_add_demo_data_to_user_table
 */
class m240725_072303_add_demo_data_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $rows = [];
        $userGenerateCount = 50;
        echo "Genereting data for {$userGenerateCount} users\n";
        for($i = 1; $i <= $userGenerateCount; $i++) {
            echo "User {$i}... ";
            $faker = \Faker\Factory::create();
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $login = $lastName . (string) rand(1,100) . $firstName . (string) rand(1,100);
            $rows[] = [
                $faker->unixTime('now'),
                $firstName,
                $lastName,
                $login,
                $faker->email(),
                \Yii::$app->getSecurity()->generatePasswordHash('password_' . $i),
                \Yii::$app->getSecurity()->generateRandomString()
            ];
            echo "Done\n";
        }
        echo "User data generation complete!\n\n";

        echo "Inserting in database... ";
        $this->batchInsert('user', [
            'created_at',
            'first_name',
            'last_name',
            'login',
            'email',
            'password',
            'auth_key'
        ], $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $_tableSchema = \Yii::$app->db->schema->getTableSchema('{{%user_meetup}}');

        if($_tableSchema) {
            if(array_key_exists('fk-user_meetup-user_id', $_tableSchema->foreignKeys)) {
                $this->dropForeignKey(
                    'fk-user_meetup-user_id',
                    'user_meetup'
                );
            }
            
            if(array_key_exists('fk-user_meetup-meetup_id', $_tableSchema->foreignKeys)) {
                $this->dropForeignKey(
                    'fk-user_meetup-meetup_id',
                    'user_meetup'
                );
            }    
        }

        $this->truncateTable('user');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240725_072303_add_demo_data_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
