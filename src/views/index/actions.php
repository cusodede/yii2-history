<?php
declare(strict_types = 1);

/**
 * @var HistoryEventAction[] $actions
 */

use cusodede\history\models\HistoryEventAction;
use yii\data\ArrayDataProvider;
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\i18n\Formatter;

?>

<?= GridView::widget([
	'dataProvider' => new ArrayDataProvider([
		'allModels' => $actions,
		'sort' => [
			'attributes' => ['type', 'attributeName']
		]
	]),
	'summary' => false,
	'formatter' => [
		'class' => Formatter::class,
		'nullDisplay' => ''
	],
	'columns' => [
		[
			'class' => DataColumn::class,
			'attribute' => 'typeName'
		],
		[
			'class' => DataColumn::class,
			'enableSorting' => false,
			'attribute' => 'attributeName'
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'attributeOldValue',
			'value' => static function(HistoryEventAction $model) {
				return HistoryEventAction::convertAttributeNewValue($model->attributeOldValue);
			}
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'attributeNewValue',
			'value' => static function(HistoryEventAction $model) {
				return HistoryEventAction::convertAttributeNewValue($model->attributeNewValue);
			}
		]
	]
]) ?>
