<?php

use yii\db\Migration;

/**
 * Class m240726_072832_add_demo_data_to_meetup_table
 */
class m240726_072832_add_demo_data_to_meetup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $rows = [];
        for($day = 1; $day <= 31; $day++) {
            echo "Generating day {$day}\n";
            for($meetup_count = 1; $meetup_count <= 10; $meetup_count++) {
                echo "Meeting №{$meetup_count}... ";

                $faker = \Faker\Factory::create();
                $title = "Top salesman of the {$faker->city()}";
                $starts_at = strtotime("24-10-{$day}" . ' ' . rand(10, 17) . ':' . rand(0, 59));
                $ends_at = $starts_at + rand(1800, 10800);
                $place = 'Room ' . rand(1, 500);
                $max_number_of_members = rand(2, 40);

                $rows[] = [
                    time(),
                    $title,
                    $starts_at,
                    $ends_at,
                    $place,
                    $max_number_of_members
                ];
                echo "Done\n";
            }
            echo "Day {$day} generation complete!\n\n";
            
        }

        echo "Inserting in database... ";
        $this->batchInsert('meetup', [
            'created_at',
            'title',
            'starts_at',
            'ends_at',
            'place',
            'max_number_of_members',
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

        $this->truncateTable('meetup');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240726_072832_add_demo_data_to_meetup_table cannot be reverted.\n";

        return false;
    }
    */
}
