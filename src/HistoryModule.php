<?php
declare(strict_types = 1);

namespace cusodede\history;

use pozitronik\traits\traits\ModuleTrait;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\db\ActiveRecordInterface;

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
		if (null === static::$_userIdentityClass) {
			$identity = static::param('userIdentityClass')??Yii::$app->user->identityClass;
			static::$_userIdentityClass = (is_callable($identity))
				?$identity()
				:$identity;
		}
		return static::$_userIdentityClass;
	}

}
