<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var ActiveRecordHistory[] $timeline
 */

use cusodede\history\models\ActiveRecordHistory;
use cusodede\history\widgets\timeline_entry\TimelineEntryWidget;
use yii\web\View;

?>

<div class="timeline">

	<!-- Timeline header -->
	<div class="timeline-header">
		<div class="timeline-header-title bg-primary">Начало</div>
	</div>
	<?php foreach ($timeline as $loggerEvent): ?>
		<?= TimelineEntryWidget::widget([
			'entry' => $loggerEvent->historyEvent->timelineEntry
		]) ?>

	<?php endforeach; ?>

</div>
