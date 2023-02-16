<?php
declare(strict_types = 1);

namespace cusodede\history\widgets\timeline_entry;

use cusodede\history\models\TimelineEntry;
use yii\base\Widget;

/**
 * Виджет элемента таймлайна
 * Class TimelineWidget
 *
 * @property TimelineEntry $entry
 */
class TimelineEntryWidget extends Widget {
	public TimelineEntry $entry;

	/**
	 * Функция возврата результата рендеринга виджета
	 * @return string
	 */
	public function run():string {
		return $this->render('timeline_entry', [
			'entry' => $this->entry
		]);
	}
}
