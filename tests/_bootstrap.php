<?php
declare(strict_types = 1);

define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

require_once __DIR__.'/../vendor/yiisoft/yii2/Yii.php';
require __DIR__.'/../vendor/autoload.php';
ob_start();