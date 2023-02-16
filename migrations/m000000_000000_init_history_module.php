<?php
declare(strict_types = 1);

use yii\db\Migration;

/**
 * Class m000000_000000_init_history_module
 */
class m000000_000000_init_history_module extends Migration {
	private const TABLE_NAME = 'sys_history';
	private const TAGS_TABLE_NAME = 'sys_history_tags';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable(self::TABLE_NAME, [
			'id' => $this->primaryKey(),
			'at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
			'user' => $this->integer()->defaultValue(null),
			'model_class' => $this->string(255)->null(),
			'model_key' => $this->integer()->null(),
			'old_attributes' => $this->binary()->comment('Old serialized attributes'),
			'new_attributes' => $this->binary()->comment('New serialized attributes'),
			'relation_model' => $this->string(255)->null(),
			'scenario' => $this->string(255)->null(),
			'event' => $this->string(255)->null(),
			'operation_identifier' => $this->string(255)->null(),
			'delegate' => $this->string(255)->null(),
		]);

		$this->createIndex('user', self::TABLE_NAME, 'user');
		$this->createIndex('model_class', self::TABLE_NAME, 'model_class');
		$this->createIndex('relation_model', self::TABLE_NAME, 'relation_model');
		$this->createIndex('model_key', self::TABLE_NAME, 'model_key');
		$this->createIndex('delegate', self::TABLE_NAME, 'delegate');
		$this->createIndex('event', self::TABLE_NAME, 'event');
		$this->createIndex('operation_identifier', self::TABLE_NAME, 'operation_identifier');
		$this->createIndex('model_class_model_key', self::TABLE_NAME, ['model_class', 'model_key']);

		$this->createTable(self::TAGS_TABLE_NAME, [
			'id' => $this->primaryKey(),
			'history' => $this->integer()->notNull(),
			'tag' => $this->string(255)->notNull()
		]);

		$this->createIndex('history_tag', self::TAGS_TABLE_NAME, ['history', 'tag'], true);

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(self::TAGS_TABLE_NAME);
		$this->dropTable(self::TABLE_NAME);

	}
}
