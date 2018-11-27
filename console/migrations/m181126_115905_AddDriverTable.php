<?php

use yii\db\Migration;
use common\models\Driver;

/**
 * Class m181126_115905_AddDriverTable
 */
class m181126_115905_AddDriverTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(Driver::tableName(), [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'birth_date' => $this->string()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(Driver::tableName());
    }
}
