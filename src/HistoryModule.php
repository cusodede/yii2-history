<?php
declare(strict_types = 1);

namespace cusodede\history;

use pozitronik\traits\traits\ModuleTrait;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\db\ActiveRecordInterface;
use yii\queue\Queue;

/**
 * Class HistoryModule
 */
class HistoryModule extends Module {
	use ModuleTrait;

	private static ?string $_userIdentityClass = null;

	/**
	 * @return string|ActiveRecordInterface
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public static function UserIdentityClass():string|ActiveRecordInterface {
		if (null === self::$_userIdentityClass) {
			$identity = static::param('userIdentityClass')??Yii::$app->user->identityClass;
			self::$_userIdentityClass = (is_callable($identity))
				?$identity()
				:$identity;
		}
		return self::$_userIdentityClass;
	}

	/**
	 * Returns configured module Queue component, or null, if not configured
	 * @return Queue|null
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public static function getQueue():?Queue {
		if (null === $queue = static::param('queue')) return null;
		return is_string($queue)
			?Yii::$app->$queue
			:Yii::createObject($queue);//@phpstan-ignore-line
	}

}
