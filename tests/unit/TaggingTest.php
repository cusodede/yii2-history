<?php
declare(strict_types = 1);
use app\models\Users;
use Codeception\Test\Unit;
use cusodede\history\models\ActiveRecordHistory;
use yii\base\InvalidConfigException;
use yii\db\Exception as DbException;

/**
 * Tests for tags support
 */
class TaggingTest extends Unit {

	/**
	 * @return void
	 * @throws Throwable
	 * @throws InvalidConfigException
	 * @throws DbException
	 */
	public function testTags():void {
		$user = Users::CreateUser()->saveAndReturn();
		static::assertFalse($user->isNewRecord);
		//Mark the last and only history change
		static::assertTrue(ActiveRecordHistory::addTag($user, 'The first mark'));
		$user->username = 'changed username';
		static::assertTrue($user->save());
		//Mark the last history change
		static::assertTrue(ActiveRecordHistory::addTag($user, 'The second mark'));

		$usersLogger = new ActiveRecordHistory(['model_class' => Users::class]);
		/** @var ActiveRecordHistory[] $usersHistory */
		$usersHistory = $usersLogger->getHistory($user->id)->all();

		self::assertEquals('The first mark', $usersHistory[0]->tag);
		self::assertEquals('The second mark', $usersHistory[1]->tag);
	}
}
