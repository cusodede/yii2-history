<?php
declare(strict_types = 1);

namespace cusodede\history\jobs;

use cusodede\history\HistoryModule;
use cusodede\history\models\active_record\History;
use cusodede\history\models\ActiveRecordHistory;
use cusodede\history\models\DelegateTrait;
use pozitronik\helpers\ArrayHelper;
use pozitronik\helpers\ModuleHelper;
use Ramsey\Uuid\Uuid;
use Throwable;
use Yii;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\queue\JobInterface;

/**
 * This job stores enqueued history changes to DB tables
 */
class HistoryJob extends History implements JobInterface {
	use DelegateTrait;

	public string $at;//todo: make writable
	public int|null $user;
	public string|null $model_class;
	public int|null $model_key;
	/**
	 * @var array|null Storage for old attributes
	 */
	public ?array $old_attributes;
	/**
	 * @var array|null Storage for new attributes
	 */
	public ?array $new_attributes;
	public string|null $relation_model;
	public string|null $scenario;
	public string|null $event;
	public string|null $operation_identifier;
	public int|null $delegate;

	public bool $storeShortClassNames = false;

	/**
	 * @inheritDoc
	 */
	public function execute($queue) {
		$saveHistoryModel = new ActiveRecordHistory([
			'storeShortClassNames' => $this->storeShortClassNames,
			'at' => $this->at,
			'user' => $this->user,
			'model_class' => $this->model_class,
			'relation_model'=> $this->relation_model,
			'event' => $this->event,
			'scenario' => $this->scenario,
			'delegate' => $this->delegate,
			'operation_identifier' => $this->operation_identifier
		]);

		$saveHistoryModel->old_attributes = $saveHistoryModel->serialize($this->old_attributes);
		$saveHistoryModel->new_attributes = $saveHistoryModel->serialize($this->new_attributes);
		return $saveHistoryModel->save();
	}

	/**
	 * Shorthand to get string identifier of stored class name (short/full class name)
	 * @param Model $model
	 * @return string
	 * @throws InvalidConfigException
	 */
	public function getStoredClassName(Model $model):string {
		return $this->storeShortClassNames
			?$model->formName()
			:get_class($model);
	}

	/**
	 * Pushes history changes to the new job and returns it
	 * @param null|ActiveRecord $model The model
	 * @param array $oldAttributes Previous attributes values
	 * @param array $newAttributes New attributes values
	 * @param ActiveRecord|null $relationModel Optional: relation model
	 * @param Event|null $event ActiveRecord operation event
	 * @param string|null $operation_identifier Optional: any arbitrary identifier. All changes with the same identifier will considered
	 * as one history change.
	 * @return static
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public static function push(?ActiveRecord $model, array $oldAttributes, array $newAttributes, ?ActiveRecord $relationModel = null, ?Event $event = null, ?string $operation_identifier = null):self {
		$historyJob = new static(['storeShortClassNames' => ArrayHelper::getValue(ModuleHelper::params(HistoryModule::class), "storeShortClassNames", false)]);
		$historyJob->setAttributes([
			'at' => date('Y-m-d H:i:s'),//store the current date, not a writing date
			'user' => Yii::$app?->user?->id,//Assuming, that the framework is configured with user identities
			'model_class' => null === $model?null:$historyJob->getStoredClassName($model),
			'model_key' => is_numeric($model->primaryKey)?$model->primaryKey:null,//$pKey can be an array
			'old_attributes' => $oldAttributes,
			'new_attributes' => $newAttributes,
			'relation_model' => null === $relationModel?null:$historyJob->getStoredClassName($relationModel),
			'event' => $event?->name,
			'scenario' => $model->scenario,
			'delegate' => self::ensureDelegate(),
			'operation_identifier' => $operation_identifier??Uuid::uuid7()->toString()
		]);

		return $historyJob;
	}
}