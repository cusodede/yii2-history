<?php /** @noinspection UsingInclusionReturnValueInspection */
declare(strict_types = 1);
use yii\caching\DummyCache;

$db = require __DIR__.'/db.php';
$history = require __DIR__.'/history.php';
$queue = require __DIR__.'/queue.php';

$config = [
	'id' => 'basic-console',
	'basePath' => dirname(__DIR__),
	'controllerNamespace' => 'app\commands',
	'bootstrap' => ['queue'],
	'modules' => [
		'history' => $history,
	],
	'aliases' => [
		'@vendor' => './vendor',
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
		'@tests' => '@app/tests',
		'@cusodede' => '@vendor/cusodede'
	],
	'components' => [
		'queue' => $queue,
		'cache' => [
			'class' => DummyCache::class,
		],
		'db' => $db
	],
	'params' => [],
];

return $config;
