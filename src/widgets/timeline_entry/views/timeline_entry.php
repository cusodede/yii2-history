<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var TimelineEntry $entry
 */

use cusodede\history\models\TimelineEntry;
use yii\web\View;

?>

<div class="timeline-entry">
	<div class="timeline-stat">
		<div class="timeline-icon"><?= $entry->user ?></div>
		<div class="timeline-time"><?= $entry->time ?></div>
	</div>
	<div class="timeline-label">
		<p class="mar-no pad-btm">
			<span class="text-semibold"><i><?= $entry->caption ?></i></span>
			<?= $entry->user ?>
		</p>
		<span><?= $entry->content ?></span>
	</div>
</div>
