<?php
declare(strict_types = 1);

use app\models\Users;
use cusodede\history\HistoryModule;

return [
	'class' => HistoryModule::class,
	'params' => [
		'userIdentityClass' => Users::class,
		'viewPath' =>  './src/views/default'
	]
];

