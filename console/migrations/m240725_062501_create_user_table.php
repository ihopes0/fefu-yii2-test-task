<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m240725_062501_create_user_table extends Migration
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

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer(),
            'first_name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'login' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'password' => $this->string()->notNull(),
            'auth_key' => $this->string(32)->defaultValue('qwertyuiopasdfghjklzxcvbnm123456'),
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

        $this->dropTable('{{%user}}');
    }
}
