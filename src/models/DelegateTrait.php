<?php
declare(strict_types = 1);

namespace cusodede\history\models;

use Yii;

/**
 * Trait to support custom user delegates
 */
trait DelegateTrait {
	/**
	 * Custom delegate support
	 * @return int|null
	 */
	protected static function ensureDelegate():?int {
		if (null !== Yii::$app?->user && method_exists(Yii::$app->user, 'getOriginalUserId')) {
			return Yii::$app->user->getOriginalUserId();
		}
		return null;
	}
}