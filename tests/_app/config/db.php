<?php
declare(strict_types = 1);

use yii\db\Connection;

return [
	'class' => Connection::class,
	'dsn' => $_ENV['DB_DSN'],
	'username' => $_ENV['DB_USER'],
	'password' => $_ENV['DB_PASS'],
	'enableSchemaCache' => false,
];