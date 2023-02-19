<?php
declare(strict_types = 1);

use yii\db\Migration;

/**
 * Class m000000_000001_history_module_fix_delegate_field
 */
class m000000_000002_history_module_add_date_index extends Migration {
	private const TABLE_NAME = 'sys_history';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createIndex('at', self::TABLE_NAME, 'at');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex('at', self::TABLE_NAME);
	}
}
