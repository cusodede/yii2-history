# yii2-history

The history of all changes in ActiveRecord models

[![Build Status](https://github.com/cusodede/yii2-history/actions/workflows/ci.yml/badge.svg)](https://github.com/cusodede/yii2-history/actions)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Run

```
php composer.phar require cusodede/yii2-history "^1.0.0"
```

or add

```
"cusodede/yii2-history": "^1.0.0"
```

to the require section of your `composer.json` file.

Requirements
------------

Yii2,
PHP >= 8.0

Usage
-----

At first, run module migrations:

```
php yii/migrate --migrationPath=@vendor/cusodede/yii2-history/migrations
```

Connect `cusodede\history\behaviors\HistoryBehavior::class` to all `ActiveRecord` models, that require to store their history:

```php
public function behaviors():array {
	return [
		'history' => [
			'class' => HistoryBehavior::class
		]
	];
}
```

If you want to get the user interface and/or change default module configuration, you need to setup the module in the application config:

```php
$config = [
	...
	'modules' => [
		'history' => [
			'class' => HistoryModule::class,
		]
	],
	...
]
```

Module configuration
--------------------

There are all the module parameters with their default values:

```php
$config = [
	...
	'modules' => [
		'history' => [
			'class' => HistoryModule::class,
			'params' => [
				'userIdentityClass' => Yii::$app->user->identityClass,
				'viewPath' => '@vendor/cusodede/yii2-history/src/views/default'
				'queue' => null,
				'storeShortClassNames' => false
			]
		]
	],
	...
]
```

Parameters description:

**userIdentityClass**: `null`|`string`|`callable`. The module tries to store some user ID with the each history record, so it is required to
provide proper user identity object. In most cases, that object is already configured in framework (and can be accessed
via `Yii::$app->user` ), but in some cases (like in console applications) it is not. It also can be overridden in some cases, so it's useful
to have option, like that.

**viewPath**: `string`. The module has a basic user interface for navigating histories. You may want to get your own custom interface for
that, so you can provide path to custom views here.

**queue**: `null`|`string`|`array`. The proper way to make all history writings asynchronous, is to push them to a queue. The `queue`
parameter allow you to configure own module queue. Just configure it like as in global configuration:

```php
$config = [
	...
	'modules' => [
		'history' => [
			'class' => HistoryModule::class,
			'params' => [
				'queue' => [
					'class' => Queue::class,
				]
			]
		]
	],
	...
]
```

Also, you can use any queues, that already configured in your application. For that case just provide string queue name:

```php
$config = [
	...
	'components' => [
		'common-queue' => [
			'class' => Queue::class,
		]
	]
	'modules' => [
		'history' => [
			'class' => HistoryModule::class,
			'params' => [
				'queue' => 'common-queue'
			]
		]
	],
	...
]
```

If parameter is skipped or set to null, all writings will be made synchronously, which may affect on the application speed.

**storeShortClassNames**: `bool`. Experimental option, that allows to store only short class name instead of fully qualified namespace
(example: `Users` instead of `app\models\Users`).

Running local tests
-------------------

Copy `/tests/.env.sample` to `/tests/.env` (change variables there, if it required in your environment) and
run `composer install` (once) and `php vendor/bin/codecept run` (to execute tests).

For Docker environment, you can just execute `docker-compose up -d --build` command to build app containers,
then run `docker exec -it yii2_history_php /bin/bash` to open bash console inside php container. Next, just do as written above.

Documentation ToDos:
-------------------

- History format;
- History tags;
- History widgets;
- Delegates support;

License
-------

GNU GPL v3.0
