<?php
declare(strict_types = 1);
use yii\caching\DummyCache;

$db = require __DIR__.'/db.php';
$history = require __DIR__.'/history.php';

$config = [
	'id' => 'basic-console',
	'basePath' => dirname(__DIR__),
	'controllerNamespace' => 'app\commands',
	'modules' => [
		'history' => $history,
	],
	'aliases' => [
		'@vendor' => './vendor',
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
		'@tests' => '@app/tests',
	],
	'components' => [
		'cache' => [
			'class' => DummyCache::class,
		],
		'db' => $db
	],
	'params' => [],
];

return $config;
