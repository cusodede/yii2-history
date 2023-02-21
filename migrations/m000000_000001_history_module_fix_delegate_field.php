<?php
declare(strict_types = 1);

use yii\db\Migration;

/**
 * Class m000000_000001_history_module_fix_delegate_field
 */
class m000000_000001_history_module_fix_delegate_field extends Migration {
	private const TABLE_NAME = 'sys_history';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		if (null === Yii::$app->db->getTableSchema(self::TABLE_NAME, true)) {
			$this->dropIndex('delegate', self::TABLE_NAME);
			$this->dropColumn(self::TABLE_NAME, 'delegate');

			$this->addColumn(self::TABLE_NAME, 'delegate', $this->integer()->null()->after('operation_identifier'));
			$this->createIndex('delegate', self::TABLE_NAME, 'delegate');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex('delegate', self::TABLE_NAME);
		$this->dropColumn(self::TABLE_NAME, 'delegate');

		$this->addColumn(self::TABLE_NAME, 'delegate', $this->string(255)->null()->after('operation_identifier'));
		$this->createIndex('delegate', self::TABLE_NAME, 'delegate');
	}
}
