<?php
declare(strict_types = 1);

namespace cusodede\history\behaviors;

use cusodede\history\models\ActiveRecordHistory;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;
use yii\helpers\ArrayHelper;

/**
 * @property ActiveRecord $owner The owner of this behavior
 * @property array|null $isRelation Relation config in relational attributes. Allow to specify that change as other model attribute change:
 *    [
 *        Model::class,// Name of the base model, which attributes are stored in that table/model,
 *        'modelKeyAttributeName', // Name of the attribute, which has relation to the base model,
 *        'relationAttributeName',// Name of the attribute with a value
 *        'modelRelatedAttributeName'// Name of the base model attribute, which value is stored in a current model (optional, name of a current class used by default)
 *    ]
 * @property callable $afterUpdate The function for full method overloading
 */
class HistoryBehavior extends Behavior {

	public array $relations = [];
	public ?array $isRelation = null;
	public mixed $afterUpdate = null;//php 8.0 has no callable type

	/**
	 *
	 * @return array
	 * @throws InvalidConfigException
	 */
	private function getModelData():array {
		if (null !== $this->isRelation) {
			if (is_array($this->isRelation)) {
				/** @var ActiveRecord $class */
				[$class, $keyAttribute, $linkAttribute] = $this->isRelation;
				$linkedAttributeName = ArrayHelper::getValue($this->isRelation, 3, $this->owner->formName());
				if (null !== $model = $class::findOne($this->owner->$keyAttribute)) {
					return [$model, [$linkedAttributeName => ArrayHelper::getValue($this->owner, $linkAttribute)], $this->owner];
				}
			} elseif (is_callable($this->isRelation)) {
				return call_user_func($this->isRelation, $this);
			}
		}
		return [$this->owner, $this->owner->attributes, null];
	}

	/**
	 * {@inheritDoc}
	 */
	public function events():array {
		return [
			ActiveRecord::EVENT_AFTER_INSERT => function(Event $event) {
				/** @var ActiveRecord $model */
				[$model, $attributes, $relation] = $this->getModelData();
				ActiveRecordHistory::push($model, [], $attributes, $relation, $event);
			},
			ActiveRecord::EVENT_AFTER_UPDATE => function(AfterSaveEvent $event) {
				if (is_callable($this->afterUpdate)) {//полностью переопределяем метод. Введено, как хак сохранения плохо тупо сделанных периодов. Нужно либо перепроектировать периоды, либо придумать логичную схему для правил
					call_user_func($this->afterUpdate, $event);
					return;
				}
				$newAttributes = [];
				/** @var ActiveRecord $model */
				[$model, , $relation] = $this->getModelData();
				foreach ($event->changedAttributes as $key => $value) {
					$newAttributes[$key] = $model->$key;
				}
				if ([] !== $newAttributes) ActiveRecordHistory::push($model, $event->changedAttributes, $newAttributes, $relation, $event);
			},
			ActiveRecord::EVENT_AFTER_DELETE => function(Event $event) {
				/** @var ActiveRecord $model */
				[$model, $attributes, $relation] = $this->getModelData();
				ActiveRecordHistory::push($model, $attributes, [], $relation, $event);
			}
		];
	}

}