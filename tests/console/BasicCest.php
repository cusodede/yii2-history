<?php
declare(strict_types = 1);
use app\models\Users;
use cusodede\history\models\ActiveRecordHistory;
use yii\console\Application;
use yii\db\ActiveRecord;

/**
 * Tests module in console environment (without Yii::$app->user, etc)
 */
class BasicCest {
	/**
	 * @param ConsoleTester $I
	 * @return void
	 */
	public function _before(ConsoleTester $I):void {
	}

	/**
	 * @param ConsoleTester $I
	 * @return void
	 */
	public function createInConsoleTest(ConsoleTester $I):void {
		$I->assertInstanceOf(Application::class, Yii::$app);
		$user = Users::CreateUser()->saveAndReturn();
		$I->assertFalse($user->isNewRecord);
		$usersLogger = new ActiveRecordHistory(['model_class' => Users::class]);
		/** @var ActiveRecordHistory[] $usersHistory */
		$usersHistory = $usersLogger->getHistory($user->id)->all();
		$I->assertCount(1, $usersHistory);
		$historyEvent = $usersHistory[0];

		$I->assertEquals(Users::class, $historyEvent->model_class);
		$I->assertEquals(ActiveRecord::EVENT_AFTER_INSERT, $historyEvent->event);
		$I->assertNull($historyEvent->delegate);
		$I->assertEquals([], $historyEvent->attributesOld);
		$I->assertEquals($user->attributes, $historyEvent->attributesNew);
	}
}
