<?php
declare(strict_types = 1);

namespace cusodede\history\controllers;

use cusodede\history\HistoryModule;
use cusodede\history\models\ActiveRecordHistory;
use cusodede\history\models\HistorySearch;
use Throwable;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class IndexController
 */
class DefaultController extends Controller {

	/**
	 * @inheritDoc
	 */
	public function getViewPath():string {
		return HistoryModule::param('viewPath', '@vendor/cusodede/yii2-history/src/views/default');
	}

	/**
	 * @return string
	 * @throws Throwable
	 */
	public function actionIndex():string {
		$params = Yii::$app->request->queryParams;// @phpstan-ignore-line
		$searchModel = new HistorySearch();
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $searchModel->search($params)
		]);
	}

	/**
	 * @param string $for
	 * @param int $id
	 * @return string
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public function actionShow(string $for, int $id):string {
		$logger = new ActiveRecordHistory([
			'model_class' => $for
		]);

		if (null === $logger->loadedModel) throw new InvalidConfigException("Model {$for} not found in application scope (module classNamesMap not configured?)");
		if (null === $logger->loadedModel = $logger->loadedModel::findOne($id)) throw new NotFoundHttpException("Model {$for}:{$id} not found");

		return $this->render('timeline', [
			'timeline' => $logger->getHistory($id)->all()
		]);
	}

	/**
	 * @param string $for
	 * @param int $id
	 * @param int $level
	 * @return string
	 * @throws InvalidConfigException
	 * @throws NotFoundHttpException
	 * @throws Throwable
	 */
	public function actionHistory(string $for, int $id, int $level = 0):string {
		$logger = new ActiveRecordHistory([
			'model_class' => $for
		]);
		if (null === $logger->loadedModel) throw new InvalidConfigException("Model {$for} not found in application scope (module classNamesMap not configured?)");
		if (null === $logger->loadedModel = $logger->loadedModel::findOne($id)) throw new NotFoundHttpException("Model {$for}:{$id} not found");

		$data = $logger->getModelHistory($level);
		foreach ($data as &$value) {
			if (!is_scalar($value)) $value = json_encode($value);//fixme: not json
		}

		return $this->render('history', [
			'for' => $for,
			'id' => $id,
			'level' => $level,
			'levelCount' => $logger->historyLevelCount,
			'model' => new DynamicModel($data)
		]);

	}

}