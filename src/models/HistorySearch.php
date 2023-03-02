<?php
declare(strict_types = 1);

namespace cusodede\history\models;

use cusodede\history\models\active_record\History;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQueryInterface;

/**
 * Class HistorySearch
 */
class HistorySearch extends History {

	/**
	 * {@inheritDoc}
	 */
	public function rules():array {
		return [
			[['id', 'user', 'model_key', 'at', 'model_class', 'delegate', 'scenario'], 'filter', 'filter' => 'trim'],
			[['id', 'user', 'model_key', 'delegate'], 'integer'],
			['at', 'date', 'format' => 'php:Y-m-d H:i'],
			[['model_class', 'event', 'scenario'], 'string']
		];
	}

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 * @throws Throwable
	 */
	public function search(array $params):ActiveDataProvider {
		$query = ActiveRecordHistory::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		$this->setSort($dataProvider);
		$this->load($params);

		if (!$this->validate()) return $dataProvider;

		$this->filterData($query);

		return $dataProvider;
	}

	/**
	 * @param ActiveDataProvider $dataProvider
	 */
	private function setSort(ActiveDataProvider $dataProvider):void {
		$dataProvider->setSort([
			'defaultOrder' => ['at' => SORT_DESC, 'id' => SORT_DESC],
			'attributes' => [
				'id',
				'at',
				'model_key',
				'model_class',
				'user',
				'event',
				'delegate',
				'scenario'
			]
		]);
	}

	/**
	 * @param ActiveQueryInterface $query
	 * @return void
	 * @throws Throwable
	 */
	private function filterData(ActiveQueryInterface $query):void {
		$query->andFilterWhere([self::tableName().'.id' => $this->id])
			->andFilterWhere([self::tableName().'.user' => $this->user])
			->andFilterWhere([self::tableName().'.delegate' => $this->delegate])
			->andFilterWhere([self::tableName().'.model_key' => $this->model_key])
			->andFilterWhere([self::tableName().'.event' => $this->event])
			->andFilterWhere(['>=', self::tableName().'.at', $this->at])
			->andFilterWhere(['like', self::tableName().'.model_class', $this->model_class])
			->andFilterWhere(['like', self::tableName().'.scenario', $this->scenario]);
	}
}