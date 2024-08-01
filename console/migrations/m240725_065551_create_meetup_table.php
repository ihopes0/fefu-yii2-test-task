<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m240725_065551_create_meetup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%meetup}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'title' => $this->string()->notNull(),
            'starts_at' => $this->integer()->notNull(),
            'ends_at' => $this->integer()->notNull(),
            'place' => $this->string()->notNull(),
            'max_number_of_members' => $this->integer()->notNull(),
            'count_participated_members' => $this->integer()->defaultValue(0),
        ], $tableOptions);
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
        
        $this->dropTable('{{%meetup}}');
    }
}
