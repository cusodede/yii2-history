<?php
declare(strict_types = 1);

use app\models\Users;
use Codeception\Test\Unit;
use cusodede\history\models\ActiveRecordHistory;
use yii\base\InvalidConfigException;
use yii\db\Exception as DbException;

/**
 * @covers ActiveRecordHistory
 */
class ActiveRecordHistoryTest extends Unit {

	/**
	 * @covers ActiveRecordHistory::push()
	 * @return void
	 * @throws Throwable
	 * @throws InvalidConfigException
	 * @throws DbException
	 */
	public function testPush():void {
		$user = Users::CreateUser()->saveAndReturn();
		static::assertFalse($user->isNewRecord);
		static::assertTrue(ActiveRecordHistory::push($user, ['a' => 'b', 'c' => 'd'], ['a' => 'x', 'z' => 'y']));
		$usersLogger = new ActiveRecordHistory(['model_class' => Users::class]);
		/** @var ActiveRecordHistory[] $usersHistory */
		$usersHistory = $usersLogger->getHistory($user->id)->all();
		static::assertCount(2, $usersHistory);
		$historyEvent = $usersHistory[1];

		static::assertEquals(Users::class, $historyEvent->model_class);
		static::assertEquals(null, $historyEvent->event);
		static::assertNull($historyEvent->delegate);
		static::assertEquals(['a' => 'b', 'c' => 'd'], $historyEvent->attributesOld);
		static::assertEquals(['a' => 'x', 'z' => 'y'], $historyEvent->attributesNew);
	}

	/**
	 * @covers ActiveRecordHistory::push()
	 * @return void
	 * @throws Throwable
	 * @throws InvalidConfigException
	 * @throws DbException
	 */
	public function testPushWithRelation():void {
		$user = Users::CreateUser()->saveAndReturn();
		static::assertFalse($user->isNewRecord);

		$relatedUser = Users::CreateUser()->saveAndReturn();
		static::assertFalse($relatedUser->isNewRecord);
		$oldAttributes = $user->attributes();
		static::assertTrue(ActiveRecordHistory::push($user, $oldAttributes, ['relatedUser' => $relatedUser->id], $relatedUser));
		$usersLogger = new ActiveRecordHistory(['model_class' => Users::class]);
		/** @var ActiveRecordHistory[] $usersHistory */
		$usersHistory = $usersLogger->getHistory($user->id)->all();
		static::assertCount(2, $usersHistory);
		$historyEvent = $usersHistory[1];

		static::assertEquals(Users::class, $historyEvent->model_class);
		static::assertEquals(null, $historyEvent->event);
		static::assertNull($historyEvent->delegate);
		static::assertEquals($oldAttributes, $historyEvent->attributesOld);
		static::assertEquals(['relatedUser' => $relatedUser->id], $historyEvent->attributesNew);
		static::assertEquals($relatedUser::class, $historyEvent->relation_model);
	}
}
