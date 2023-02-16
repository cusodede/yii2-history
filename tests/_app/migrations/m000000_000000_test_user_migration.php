<?php
declare(strict_types = 1);

use yii\db\Migration;

/**
 * Class m000000_000000_test_user_migration
 */
class m000000_000000_test_user_migration extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('users', [
			'id' => $this->primaryKey(),
			'username' => $this->string(255)->notNull()->comment('Отображаемое имя пользователя'),
			'login' => $this->string(64)->notNull()->comment('Логин'),
			'password' => $this->string(255)->notNull()->comment('Хеш пароля'),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('users');
	}

}
