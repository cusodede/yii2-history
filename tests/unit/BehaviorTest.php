<?php
declare(strict_types = 1);
use app\models\Users;
use Codeception\Test\Unit;
use cusodede\history\models\ActiveRecordHistory;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Exception as DbException;

/**
 *
 */
class BehaviorTest extends Unit {

	/**
	 * @return void
	 * @throws Throwable
	 * @throws InvalidConfigException
	 * @throws DbException
	 */
	public function testCreate():void {
		$user = Users::CreateUser()->saveAndReturn();
		static::assertFalse($user->isNewRecord);
		$usersLogger = new ActiveRecordHistory(['model_class' => Users::class]);
		/** @var ActiveRecordHistory[] $usersHistory */
		$usersHistory = $usersLogger->getHistory($user->id)->all();
		static::assertCount(1, $usersHistory);
		$historyEvent = $usersHistory[0];

		static::assertEquals(Users::class, $historyEvent->model_class);
		static::assertEquals($user->id, $historyEvent->id);
		static::assertEquals(ActiveRecord::EVENT_AFTER_INSERT, $historyEvent->event);
		static::assertNull($historyEvent->delegate);
		static::assertEquals([], $historyEvent->attributesOld);
		static::assertEquals($user->attributes, $historyEvent->attributesNew);
	}

	/**
	 * @return void
	 */
	public function testChange():void {

	}

	/**
	 * @return void
	 */
	public function testDelete():void {

	}
}
