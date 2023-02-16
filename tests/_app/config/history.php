<?php
declare(strict_types = 1);

use app\models\Users;
use cusodede\history\models\active_record\History;

return [
	'class' => History::class,
	'params' => [
		'userIdentityClass' => Users::class,
		'viewPath' =>  './src/views/history'
	]
];

