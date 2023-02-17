<?php
declare(strict_types = 1);
use app\models\Users;
use Codeception\Exception\ModuleException;
use cusodede\history\HistoryModule;
use yii\base\InvalidConfigException;
use yii\db\Exception as DbException;

/**
 *
 */
class ViewPathCest {
	/**
	 * @param FunctionalTester $I
	 * @return void
	 * @throws DbException
	 * @throws ModuleException
	 * @throws Throwable
	 */
	public function TestActions(FunctionalTester $I):void {
		$user = Users::CreateUser()->saveAndReturn();
		Yii::$app->setModule('history', [
			'class' => HistoryModule::class,
			'params' => [
				'userIdentityClass' => Users::class,
				'viewPath' => './src/views/default'
			]
		]);

		$I->amLoggedInAs($user);
		$I->amOnRoute('history/default');
		$I->seeResponseCodeIs(200);

		$I->amOnRoute('history/default/show', ['for' => Users::class, 'id' => $user->id]);
		$I->seeResponseCodeIs(200);
		$I->amOnRoute('history/default/history', ['for' => Users::class, 'id' => $user->id]);
		$I->seeResponseCodeIs(200);

	}

	/**
	 * @param FunctionalTester $I
	 * @return void
	 * @throws DbException
	 * @throws ModuleException
	 * @throws Throwable
	 */
	public function TestActionErrors(FunctionalTester $I):void {
		$user = Users::CreateUser()->saveAndReturn();
		Yii::$app->setModule('history', [
			'class' => HistoryModule::class,
			'params' => [
				'userIdentityClass' => Users::class,
				'viewPath' => './src/views/default'
			]
		]);

		$I->amLoggedInAs($user);
		$I->amOnRoute('history/default/index');
		$I->seeResponseCodeIs(200);

		//it's not possible to use expectThrowable() here, because any exceptions will be caught by Yii2 Connector
		$I->amOnRoute('history/default/show', ['for' => Users::class, 'id' => $user->id + 1]);//wrong id
		//but we can rely on response code
		$I->seeResponseCodeIs(404);
		//and on response
		$I->assertEquals(sprintf("Model %s:%d not found", Users::class, $user->id + 1), $I->grabResponse());

		$I->expectThrowable(InvalidConfigException::class, static function() use ($user, $I):void {
			$I->amOnRoute('history/default/show', ['for' => 'UnknownClass', 'id' => $user->id]);//wrong class
		});
		$I->seeResponseCodeIs(404);

		$I->amOnRoute('history/default/history', ['for' => Users::class, 'id' => $user->id + 1]);//wrong id
		$I->seeResponseCodeIs(404);
		$I->assertEquals(sprintf("Model %s:%d not found", Users::class, $user->id + 1), $I->grabResponse());

		$I->expectThrowable(InvalidConfigException::class, static function() use ($user, $I):void {
			$I->amOnRoute('history/default/history', ['for' => 'UnknownClass', 'id' => $user->id]);//wrong class
		});
		$I->seeResponseCodeIs(404);

	}

	/**
	 * @param FunctionalTester $I
	 * @return void
	 * @throws DbException|ModuleException
	 */
	public function TestViewCustomPath(FunctionalTester $I):void {
		$user = Users::CreateUser()->saveAndReturn();
		Yii::$app->setModule('history', [
			'class' => HistoryModule::class,
			'params' => [
				'userIdentityClass' => Users::class,
				'viewPath' => './tests/_app/views/custom'
			]
		]);

		$I->amLoggedInAs($user);
		$I->amOnRoute('history/default');
		$I->seeResponseCodeIs(200);
		$I->seeResponseContains('custom index');
		$I->amOnRoute('history/default/show', ['for' => Users::class, 'id' => $user->id]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContains('custom timeline');
		$I->amOnRoute('history/default/history', ['for' => Users::class, 'id' => $user->id]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContains('custom history');
	}

}