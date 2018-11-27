<?php

use yii\db\Migration;
use common\models\Bus;

/**
 * Class m181126_115842_AddBusTable
 */
class m181126_115842_AddBusTable extends Migration
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

        $this->createTable(Bus::tableName(), [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'average_speed' => $this->float()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(Bus::tableName());
    }
}
