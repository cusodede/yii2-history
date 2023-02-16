<?php
declare(strict_types = 1);

namespace cusodede\history\models;

use Throwable;
use yii\base\Model;
use pozitronik\helpers\ArrayHelper;

/**
 * Class HistoryEventAction
 *
 * @property int $type Тип события
 * @property null|string $attributeName Изменившийся атрибут
 * @property mixed $attributeOldValue Значение атрибута до изменения
 * @property mixed $attributeNewValue Значение атрибута после изменения
 *
 * @property string $typeName
 *
 */
class HistoryEventAction extends Model {
	public const ATTRIBUTE_CREATED = 0;
	public const ATTRIBUTE_CHANGED = 1;
	public const ATTRIBUTE_DELETED = 2;

	public const ATTRIBUTE_TYPE_NAMES = [
		self::ATTRIBUTE_CREATED => 'Added',
		self::ATTRIBUTE_CHANGED => 'Changed',
		self::ATTRIBUTE_DELETED => 'Deleted'
	];

	public ?int $type = null;
	public ?string $attributeName = null;
	public mixed $attributeOldValue = null;
	public mixed $attributeNewValue = null;

	/**
	 * {@inheritDoc}
	 */
	public function attributeLabels():array {
		return [
			'type' => 'Change',
			'typeName' => 'Action',
			'attributeName' => 'Attribute',
			'attributeOldValue' => 'Old value',
			'attributeNewValue' => 'New value'
		];
	}

	/**
	 * @return null|string
	 * @throws Throwable
	 */
	public function getTypeName():?string {
		return ArrayHelper::getValue(self::ATTRIBUTE_TYPE_NAMES, $this->type);
	}

	/**
	 * @param mixed $attribute
	 * @return mixed
	 */
	public static function convertAttributeNewValue(mixed $attribute) {
		if (is_array($attribute)) {
			return json_encode($attribute, JSON_PRETTY_PRINT);//fixme not json
		}

		if (is_bool($attribute)) {
			return $attribute?'true':'false';
		}
		return $attribute;
	}
}