<?php
declare(strict_types = 1);
use app\models\Users;
use cusodede\history\HistoryModule;
use yii\db\Exception as DbException;

/**
 *
 */
class ViewPathCest {
	/**
	 * @param FunctionalTester $I
	 * @return void
	 * @throws DbException
	 */
	public function TestCustomViewPath(FunctionalTester $I):void {
		$appDir = Yii::getAlias('@app');
		$user = Users::CreateUser()->saveAndReturn();
		Yii::$app->setModule('history', [
			'class' => HistoryModule::class,
			'params' => [
				'userIdentityClass' => Users::class,
				'viewPath' => './src/views/history'
			]
		]);

		$I->amLoggedInAs($user);
		$I->amOnRoute('history/index');
	}

}