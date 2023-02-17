<?php
declare(strict_types = 1);
use app\models\Users;
use Codeception\Test\Unit;
use cusodede\history\models\ActiveRecordHistory;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Exception as DbException;
use yii\db\StaleObjectException;

/**
 * Simple create/update/delete tests
 */
class HistoryTest extends Unit {

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
		static::assertEquals(ActiveRecord::EVENT_AFTER_INSERT, $historyEvent->event);
		static::assertNull($historyEvent->delegate);
		static::assertEquals([], $historyEvent->attributesOld);
		static::assertEquals($user->attributes, $historyEvent->attributesNew);
	}

	/**
	 * @return void
	 * @throws DbException
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public function testChange():void {
		$user = Users::CreateUser()->saveAndReturn();
		static::assertFalse($user->isNewRecord);
		$oldAttributes = $user->attributes;
		$user->username = 'changed username';
		static::assertTrue($user->save());

		$usersLogger = new ActiveRecordHistory(['model_class' => Users::class]);
		/** @var ActiveRecordHistory[] $usersHistory */
		$usersHistory = $usersLogger->getHistory($user->id)->all();
		static::assertCount(2, $usersHistory);
		$historyEvent = $usersHistory[1];

		static::assertEquals(Users::class, $historyEvent->model_class);
		static::assertEquals(ActiveRecord::EVENT_AFTER_UPDATE, $historyEvent->event);
		static::assertNull($historyEvent->delegate);
		static::assertEquals($oldAttributes['username'], $historyEvent->attributesOld['username']);
		static::assertEquals($user->attributes['username'], $historyEvent->attributesNew['username']);
	}

	/**
	 * @return void
	 * @throws DbException
	 * @throws InvalidConfigException
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function testDelete():void {
		$user = Users::CreateUser()->saveAndReturn();
		$userId = $user->id;
		static::assertFalse($user->isNewRecord);
		$oldAttributes = $user->attributes;
		static::assertEquals(1, $user->delete());

		$usersLogger = new ActiveRecordHistory(['model_class' => Users::class]);
		/** @var ActiveRecordHistory[] $usersHistory */
		$usersHistory = $usersLogger->getHistory($userId)->all();
		static::assertCount(2, $usersHistory);
		$historyEvent = $usersHistory[1];

		static::assertEquals(Users::class, $historyEvent->model_class);
		static::assertEquals(ActiveRecord::EVENT_AFTER_DELETE, $historyEvent->event);
		static::assertNull($historyEvent->delegate);
		static::assertEquals($oldAttributes, $historyEvent->attributesOld);
		static::assertEquals([], $historyEvent->attributesNew);
	}
}
