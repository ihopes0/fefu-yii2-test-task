<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_meetup}}`.
 */
class m240725_070930_create_user_meetup_table extends Migration
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

        $this->createTable('{{%user_meetup}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer(),
            'user_id' => $this->integer()->notNull(),
            'meetup_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $_tableSchema = \Yii::$app->db->schema->getTableSchema('{{%user_meetup}}');

        if(!array_key_exists('fk-user_meetup-user_id', $_tableSchema->foreignKeys)) {
            $this->addForeignKey(
                'fk-user_meetup-user_id',
                'user_meetup',
                'user_id',
                'user',
                'id',
                'CASCADE'
            );
        }
        if(!array_key_exists('fk-user_meetup-meetup_id', $_tableSchema->foreignKeys)) {
            $this->addForeignKey(
                'fk-user_meetup-meetup_id',
                'user_meetup',
                'meetup_id',
                'meetup',
                'id',
                'CASCADE'
            );
        }
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

        $this->dropTable('{{%user_meetup}}');
    }
}
