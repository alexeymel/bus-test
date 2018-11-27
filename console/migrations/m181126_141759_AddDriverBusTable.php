<?php

use yii\db\Migration;
use common\models\Driver;
use common\models\Bus;

/**
 * Class m181126_141759_AddDriverBusTable
 */
class m181126_141759_AddDriverBusTable extends Migration
{
    /** @var string $tableName */
    private $tableName;

    public function init()
    {
        parent::init();

        $this->tableName = Driver::tableName() . '_' . Bus::tableName();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'driver_id' => $this->integer()->notNull(),
            'bus_id' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
