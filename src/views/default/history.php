<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var int $for
 * @var int $id
 * @var int $level
 * @var int $levelCount
 * @var ActiveRecord $model
 */

use cusodede\history\HistoryModule;
use yii\data\Pagination;
use yii\db\ActiveRecord;
use yii\web\View;
use yii\widgets\DetailView;
use yii\widgets\LinkPager;

?>

<?= LinkPager::widget([
	'pagination' => new Pagination([
		'totalCount' => $levelCount - 1,
		'page' => $level - 1,
		'pageSize' => 1,
		'pageParam' => 'level',
		'pageSizeParam' => false,
		'route' => HistoryModule::to(['/index/history', 'for' => $for, 'id' => $id])//todo: не знаю как, нет времени фиксить
	]),
	'hideOnSinglePage' => false
]) ?>

<?= DetailView::widget([
	'model' => $model
]) ?>
