<?php
declare(strict_types = 1);
use app\models\Users;
use Codeception\Test\Unit;
use cusodede\history\HistoryModule;
use cusodede\history\jobs\HistoryJob;
use cusodede\history\models\ActiveRecordHistory;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\queue\file\Queue;

/**
 * @covers HistoryJob
 */
class HistoryJobTest extends Unit {

	/**
	 * Tests that module jobs have no queue component by default
	 * @return void
	 * @throws Throwable
	 * @throws InvalidConfigException
	 */
	public function testNoQueue():void {
		Yii::$app->setModule('history', [
			'class' => HistoryModule::class,
			'params' => [
				'userIdentityClass' => Users::class,
				'viewPath' => './src/views/default',
			]
		]);

		static::assertNull(HistoryModule::getQueue());
	}

	/**
	 * Tests that module jobs can work on previously configured queue component
	 * @return void
	 * @throws Throwable
	 * @throws InvalidConfigException
	 */
	public function testQueue():void {
		Yii::$app->setModule('history', [
			'class' => HistoryModule::class,
			'params' => [
				'userIdentityClass' => Users::class,
				'viewPath' => './src/views/default',
				'queue' => 'queue'
			]
		]);
		/** Yii::$app->queue is configured in _app/config/main.php */
		/** @noinspection PhpUndefinedFieldInspection */
		static::assertInstanceOf(Yii::$app->queue::class, HistoryModule::getQueue());
	}

	/**
	 * Tests that module can configure and use its own queue as well
	 * @return void
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public function testCustomQueue():void {
		Yii::$app->setModule('history', [
			'class' => HistoryModule::class,
			'params' => [
				'userIdentityClass' => Users::class,
				'viewPath' => './src/views/default',
				'queue' => [
					'class' => Queue::class,
					'path' => '@runtime/queues/common',
					'ttr' => 10
				]
			]
		]);

		static::assertInstanceOf(Queue::class, HistoryModule::getQueue());
	}

	/**
	 * @return void
	 * @throws Throwable
	 */
	public function testExecute():void {
		Yii::$app->setModule('history', [
			'class' => HistoryModule::class,
			'params' => [
				'userIdentityClass' => Users::class,
				'viewPath' => './src/views/default',
				'queue' => [
					'class' => Queue::class,
					'path' => '@runtime/queues/common',
					'ttr' => 10
				]
			]
		]);

		/** @var Queue $queue */
		$queue = HistoryModule::getQueue();
		$queue->clear();//just to be sure

		$user = Users::CreateUser()->saveAndReturn();
		static::assertFalse($user->isNewRecord);
		$oldAttributes = $user->attributes;
		$user->username = 'changed username';
		static::assertTrue($user->save());

		$usersLogger = new ActiveRecordHistory(['model_class' => Users::class]);
		/** @var ActiveRecordHistory[] $usersHistory */
		$usersHistory = $usersLogger->getHistory($user->id)->all();
		static::assertCount(0, $usersHistory);//history shouldn't be written

		$queue->run(false, 10);

		$usersHistory = $usersLogger->getHistory($user->id)->all();
		static::assertCount(2, $usersHistory);
		$historyEvent = $usersHistory[1];
		static::assertEquals(Users::class, $historyEvent->model_class);
		static::assertEquals(ActiveRecord::EVENT_AFTER_UPDATE, $historyEvent->event);
		static::assertNull($historyEvent->delegate);
		static::assertEquals($oldAttributes['username'], $historyEvent->attributesOld['username']);
		static::assertEquals($user->attributes['username'], $historyEvent->attributesNew['username']);
	}
}
