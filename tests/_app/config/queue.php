<?php
declare(strict_types = 1);

use yii\queue\file\Queue;

return [
	'class' => Queue::class,
	'path' => '@runtime/queues/common',
	'ttr' => 10
];