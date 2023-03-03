<?php
declare(strict_types = 1);

namespace cusodede\history\models;

use Exception;
use pozitronik\helpers\ReflectionHelper;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use pozitronik\helpers\ArrayHelper;

/**
 * Class HistoryEvent
 * Расшифровка событий ежи
 *
 * @property int $eventType Что произошло. Добавление/изменение/удаление/свой тип
 * @property null|string $eventTypeName Строковое название события, null - по умолчанию
 * @property string|null $eventIcon Иконка?
 * @property string $eventTime Во сколько сделал
 * @property string $objectName Где сделал (имя класса)
 * @property null|int $subject Кто сделал
 * @property HistoryEventAction[] $actions Набор изменений внутри одного события.
 * @property null|string $eventCaption Переопределить типовой заголовок события
 *
 * @property null|string|callable|array|false $actionsFormatter
 * @property TimelineEntry $timelineEntry
 */
class HistoryEvent extends Model implements HistoryEventInterface {
	public ?int $eventType = null;
	public ?string $eventCaption = null;
	public ?string $eventIcon = null;
	public ?string $eventTime = null;
	public ?string $objectName = null;
	public ?int $subject = null;
	public mixed $actionsFormatter = null;
	/**
	 * @var HistoryEventAction[]|null
	 */
	public ?array $actions = null;
	/* seems to be unused
	public $subjectId;
	*/

	/**
	 * Converts log event to timeline entry
	 * @return TimelineEntry
	 * @throws Throwable
	 */
	public function getTimelineEntry():TimelineEntry {
		if (null === $this->actionsFormatter) {
			$content = self::ActionsFormatterDefault($this->actions);//default formatter
		} elseif (is_string($this->actionsFormatter)) {
			$content = $this->actionsFormatter;
		} elseif (ReflectionHelper::is_closure($this->actionsFormatter)) {
			$content = call_user_func($this->actionsFormatter, $this->actions);
		} elseif (is_array($this->actionsFormatter)) {//['view', parameters]
			$view = ArrayHelper::getValue($this->actionsFormatter, 0, new InvalidConfigException('actionsFormatter array config must contain view path as first item'));
			$parameters = (array)ArrayHelper::getValue($this->actionsFormatter, 1, []);
			$parameters['actions'] = $this->actions;
			$content = Yii::$app->view->render($view, $parameters);

		} else $content = null;

		return new TimelineEntry([
			'time' => $this->eventTime,
			'caption' => $this->eventCaption??$this->eventTypeName,
			'user' => $this->subject,
			'content' => $content
		]);
	}

	/**
	 * Форматирование массива событий по умолчанию
	 * @param HistoryEventAction[] $actions
	 * @return string
	 * @throws Exception
	 */
	public static function ActionsFormatterDefault(array $actions):string {
		return Yii::$app->view->render('actions', ['actions' => $actions]);
	}

	/**
	 * @return null|string
	 * @throws Throwable
	 */
	public function getEventTypeName():?string {
		return ArrayHelper::getValue(self::EVENT_TYPE_NAMES, $this->eventType);
	}
}
