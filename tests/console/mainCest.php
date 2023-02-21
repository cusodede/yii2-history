<?php
declare(strict_types = 1);
use yii\console\Application;

/**
 *
 */
class mainCest {
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
	}
}
