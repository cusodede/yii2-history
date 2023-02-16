<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var HistorySearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

use cusodede\history\models\ActiveRecordHistory;
use cusodede\history\models\HistoryEventInterface;
use cusodede\history\models\HistorySearch;
use kartik\datetime\DateTimePicker;
use kartik\grid\DataColumn;
use pozitronik\widgets\BadgeWidget;
use yii\data\ActiveDataProvider;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\i18n\Formatter;
use yii\web\View;
use pozitronik\helpers\Utils;

?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'summary' => false,
	'showOnEmpty' => false,
	'resizableColumns' => true,
	'responsive' => true,
	'export' => false,
	'panel' => [
		'heading' => $this->title.(($dataProvider->totalCount > 0)?" (".Utils::pluralForm($dataProvider->totalCount, ['запись', 'записи', 'записей']).")":" (нет записей)"),
	],
	'formatter' => [
		'class' => Formatter::class,
		'nullDisplay' => ''
	],
	'columns' => [
		'id',
		[
			'attribute' => 'user',
			'format' => 'raw',
			'value' => static fn(ActiveRecordHistory $model):string => BadgeWidget::widget([
				'items' => $model->relatedUser,
				'subItem' => 'id',
				'useBadges' => false,
			])
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'event',
			'value' => static fn(ActiveRecordHistory $model) => $model->historyEvent->eventCaption,
			'format' => 'raw',
			'filter' => HistoryEventInterface::EVENT_TYPE_FRAMEWORK_NAMES,
			'filterWidgetOptions' => [
				'pluginOptions' => ['allowClear' => true, 'placeholder' => '']
			]
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'tag',
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'at',
			'filterType' => DateTimePicker::class,
			'filterWidgetOptions' => [
				'type' => DateTimePicker::TYPE_INPUT,
				'pluginOptions' => [
					'alwaysShowCalendars' => true
				]
			]
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'model_class',
			'value' => static fn(ActiveRecordHistory $model) => null === $model->model_key?$model->model_class:Html::a($model->model_class, [
				'show', 'for' => $model->model_class, 'id' => $model->model_key
			]),
			'format' => 'raw',
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'relation_model',
			'format' => 'raw',
		],
		[
			'attribute' => 'model_key',
			'value' => static fn(ActiveRecordHistory $model) => null === $model->model_key?$model->model_key:Html::a($model->model_key, [
				'history', 'for' => $model->model_class, 'id' => $model->model_key
			]),
			'format' => 'raw'
		],
		[
			'attribute' => 'actions',
			'filter' => false,
			'format' => 'raw',
			'value' => static fn(ActiveRecordHistory $model) => $model->historyEvent->timelineEntry->content
		],
		'scenario',
		[
			'attribute' => 'delegate',
			'format' => 'raw',
			'value' => static fn(ActiveRecordHistory $model):string => BadgeWidget::widget([
				'items' => $model->relatedUserDelegated,
				'subItem' => 'id',
				'useBadges' => false,
			])
		]
	]
]) ?>

