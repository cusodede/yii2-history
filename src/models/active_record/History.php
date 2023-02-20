<?php
declare(strict_types = 1);

namespace cusodede\history\models\active_record;

use cusodede\history\HistoryModule;
use pozitronik\helpers\ArrayHelper;
use pozitronik\helpers\ModuleHelper;
use Throwable;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property int $id
 * @property string $at CURRENT_TIMESTAMP
 * @property int|null $user id пользователя, совершившего изменение
 * @property string|null $model_class Класс (FQN, либо алиас, сопоставленный в конфиге)
 * @property int|null $model_key Первичный ключ модели, если есть. Составные ключи не поддерживаются.
 * @property string|null $old_attributes Old serialized attributes
 * @property string|null $new_attributes New serialized attributes
 * @property string|null $relation_model Опциональная связанная модель (используется при построении представления истории)
 * @property string|null $scenario Опциональный сценарий события
 * @property string|null $event Событие, вызвавшее сохранение слепка истории
 * @property string|null $operation_identifier Уникальный идентификатор (обычно клиентский csrf), связывающий несколько последовательных изменений, происходящих в одном событии
 * @property int|null $delegate Опционально: идентификатор "перекрывающего" пользователя, если поддерживается приложением
 *
 * @property null|IdentityInterface $relatedUser Пользователь, который вызвал изменение
 * @property null|IdentityInterface $relatedUserDelegated Пользователь, который вызвал изменение за другого (авторизовался под user)
 */
class History extends ActiveRecord {
	/**
	 * {@inheritDoc}
	 */
	public static function tableName():string {
		return ArrayHelper::getValue(ModuleHelper::params(HistoryModule::class), 'tableName', 'sys_history');
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['user', 'model_key', 'delegate'], 'integer'],
			[['old_attributes', 'new_attributes'], 'string'],
			[['at', 'model_class', 'relation_model', 'scenario', 'event', 'operation_identifier'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'at' => 'Время события',
			'user' => 'Пользователь',
			'model_class' => 'Модель',
			'relation_model' => 'Связанная модель',
			'old_attributes' => 'Прежние данные',
			'new_attributes' => 'Изменения',
			'eventType' => 'Тип события',
			'scenario' => 'Сценарий',
			'event' => 'Событие',
			'delegate' => 'Делегировавший пользователь',
			'operation_identifier' => 'Идентификатор операции'
		];
	}

	/**
	 * @return ActiveQuery
	 * @throws Throwable
	 * @throws InvalidConfigException
	 */
	public function getRelatedUser():ActiveQuery {
		return $this->hasOne(HistoryModule::UserIdentityClass(), ['id' => 'user']);
	}

	/**
	 * @return ActiveQuery
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public function getRelatedUserDelegated():ActiveQuery {
		return $this->hasOne(HistoryModule::UserIdentityClass(), ['id' => 'delegate']);
	}
}
