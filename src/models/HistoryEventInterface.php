<?php
declare(strict_types = 1);

namespace cusodede\history\models;

use yii\db\ActiveRecord;

/**
 * Interface HistoryEventInterface
 *
 * @property int $eventType Что сделал
 * @property null|string $eventTypeName Что сделал
 * @property string|null $eventIcon Иконка?
 * @property string $eventTime Во сколько сделал
 * @property string $objectName Где сделал
 * @property null|int $subject Кто сделал
 * @property HistoryEventAction[] $actions Что произошло
 * @property null|string $eventCaption Переопределить типовой заголовок события
 *
 * @property TimelineEntry $timelineEntry
 */
interface HistoryEventInterface {
	public const EVENT_CREATED = 0;
	public const EVENT_CHANGED = 1;
	public const EVENT_DELETED = 2;

	public const EVENT_CREATED_LABEL = 'Record added';
	public const EVENT_CHANGED_LABEL = 'Record changed';
	public const EVENT_DELETED_LABEL = 'Record deleted';

	public const EVENT_CREATED_FRAMEWORK_LABEL = ActiveRecord::EVENT_AFTER_INSERT;
	public const EVENT_CHANGED_FRAMEWORK_LABEL = ActiveRecord::EVENT_AFTER_UPDATE;
	public const EVENT_DELETED_FRAMEWORK_LABEL = ActiveRecord::EVENT_AFTER_DELETE;

	public const EVENT_TYPE_NAMES = [
		self::EVENT_CREATED => self::EVENT_CREATED_LABEL,
		self::EVENT_CHANGED => self::EVENT_CHANGED_LABEL,
		self::EVENT_DELETED => self::EVENT_DELETED_LABEL
	];

	public const EVENT_TYPE_FRAMEWORK_NAMES = [
		ActiveRecord::EVENT_AFTER_INSERT => self::EVENT_CREATED_LABEL,
		ActiveRecord::EVENT_AFTER_UPDATE => self::EVENT_CHANGED_LABEL,
		ActiveRecord::EVENT_AFTER_DELETE => self::EVENT_DELETED_LABEL
	];
}